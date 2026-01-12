<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class PaytmGatewayApi extends ResourceController
{
    protected $format = 'json';

    /**
     * Paytm Configuration
     */
    private $paytmConfig;

    /**
     * Get Paytm Config (Lazy Loading)
     */
    private function getPaytmConfig()
    {
        if ($this->paytmConfig === null) {
            $this->paytmConfig = new \App\Config\Paytm();
        }
        return $this->paytmConfig;
    }




    /**
     * Initiate UPI Payment (Direct UPI Intent)
     * Creates transaction and returns UPI intent link
     */
    public function initiateUpiPayment()
    {
        $request = \Config\Services::request();
        $input = $request->getJSON(true);

        $vendorId = session()->get('id') ?? $input['vendor_id'] ?? null;
        $amount = $input['amount'] ?? 0;
        $customerVpa = $input['customer_vpa'] ?? null; // Optional: customer's UPI ID

        if (!$vendorId || $amount <= 0) {
            return $this->fail('Invalid request');
        }

        // Generate unique order ID
        $orderId = 'PTM_UPI_' . strtoupper(uniqid());
        $custId = 'CUST_' . $vendorId;

        // Create payment record
        $paymentModel = new \App\Models\PaymentModel();
        $paymentData = [
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'txn_id' => $orderId,
            'status' => 'pending',
            'method' => 'paytm_upi',
            'gateway_name' => 'paytm',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $paymentModel->insert($paymentData);
        } catch (\Exception $e) {
            return $this->failServerError('Database error: ' . $e->getMessage());
        }

        // Format amount properly (2 decimal places)
        $fmtAmount = number_format((float) $amount, 2, '.', '');

        // Create payment note
        $note = "Payment for Order {$orderId}";

        // URL encode the name and note
        $config = $this->getPaytmConfig();
        $encodedName = urlencode($config->payeeName);
        $encodedNote = urlencode($note);

        // Standard UPI Intent Link
        $upiIntent = "upi://pay?pa={$config->upiVpa}&pn={$encodedName}&tr={$orderId}&am={$fmtAmount}&cu=INR&tn={$encodedNote}";

        // Paytm-Specific Deep Link with signature
        $signatureData = "{$config->upiVpa}|{$config->payeeName}|{$fmtAmount}|{$orderId}|{$config->merchantKey}";
        $signature = base64_encode(hash('sha256', $signatureData, true));

        $paytmIntent = "paytmmp://cash_wallet?pa={$config->upiVpa}&pn={$encodedName}&am={$fmtAmount}&cu=INR&tn={$encodedNote}&tr={$orderId}&mc={$config->merchantCode}&mid={$config->merchantId}&sign={$signature}&featuretype=money_transfer";

        // Short Link for QR Code
        $qrLink = "upi://pay?pa={$config->upiVpa}&pn={$encodedName}&tr={$orderId}&am={$fmtAmount}&cu=INR";

        return $this->respond([
            'success' => true,
            'order_id' => $orderId,
            'payment_type' => 'upi_intent',
            'upi_intent' => $upiIntent,
            'paytm_intent' => $paytmIntent,
            'qr_string' => $qrLink,
            'payment_info' => [
                'amount' => $amount,
                'currency' => 'INR',
                'payee_vpa' => $config->upiVpa,
                'payee_name' => $config->payeeName
            ],
            'instructions' => [
                'message' => 'Scan QR code or click UPI link to pay',
                'note' => 'Payment will be automatically verified'
            ]
        ]);
    }

    /**
     * Get callback URL dynamically
     */
    private function getCallbackUrl()
    {
        return base_url('api/paytm/callback');
    }

    /**
     * Initiate Paytm Payment
     * Creates transaction and returns payment page URL
     */
    public function initiatePayment()
    {
        $request = \Config\Services::request();
        $input = $request->getJSON(true);

        $vendorId = session()->get('id') ?? $input['vendor_id'] ?? null;
        $amount = $input['amount'] ?? 0;

        if (!$vendorId || $amount <= 0) {
            return $this->fail('Invalid request');
        }

        // Generate unique order ID
        $orderId = 'PTM_' . strtoupper(uniqid());
        $custId = 'CUST_' . $vendorId;

        // Create payment record
        $paymentModel = new \App\Models\PaymentModel();
        $paymentData = [
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'txn_id' => $orderId,
            'status' => 'pending',
            'method' => 'paytm_gateway',
            'gateway_name' => 'paytm',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $paymentModel->insert($paymentData);
        } catch (\Exception $e) {
            return $this->failServerError('Database error: ' . $e->getMessage());
        }

        // Prepare Paytm parameters
        $config = $this->getPaytmConfig();
        $paytmParams = [
            'MID' => $config->merchantId,
            'WEBSITE' => $config->website,
            'INDUSTRY_TYPE_ID' => $config->industryType,
            'CHANNEL_ID' => $config->channelId,
            'ORDER_ID' => $orderId,
            'CUST_ID' => $custId,
            'TXN_AMOUNT' => number_format($amount, 2, '.', ''),
            'CALLBACK_URL' => $this->getCallbackUrl(),
        ];

        // Generate checksum
        $checksum = $this->generateChecksum($paytmParams, $config->merchantKey);
        $paytmParams['CHECKSUMHASH'] = $checksum;

        // Store params in session for verification
        session()->set('paytm_params_' . $orderId, $paytmParams);

        return $this->respond([
            'success' => true,
            'order_id' => $orderId,
            'payment_url' => $config->getApiUrl('transaction'),
            'params' => $paytmParams,
            'amount' => $amount
        ]);
    }

    /**
     * Check Payment Status
     * Queries Paytm API to check if payment is completed
     */
    public function checkStatus()
    {
        $request = \Config\Services::request();
        $input = $request->getJSON(true);

        $orderId = $input['order_id'] ?? null;

        if (!$orderId) {
            return $this->fail('Order ID required');
        }

        $paymentModel = new \App\Models\PaymentModel();
        $payment = $paymentModel->where('txn_id', $orderId)->first();

        if (!$payment) {
            return $this->failNotFound('Payment not found');
        }

        // If already completed, return status
        if ($payment['status'] === 'success') {
            return $this->respond([
                'status' => 'success',
                'message' => 'Payment completed',
                'txn_id' => $payment['utr'] ?? $orderId
            ]);
        }

        if ($payment['status'] === 'failed') {
            return $this->respond([
                'status' => 'error',
                'error' => 'Payment failed'
            ]);
        }

        // Query Paytm for status
        $statusResponse = $this->queryPaytmStatus($orderId);

        if ($statusResponse) {
            $status = $statusResponse['STATUS'] ?? 'PENDING';
            $txnId = $statusResponse['TXNID'] ?? null;
            $bankTxnId = $statusResponse['BANKTXNID'] ?? null;

            if ($status === 'TXN_SUCCESS') {
                // Update payment as successful
                $paymentModel->update($payment['id'], [
                    'status' => 'success',
                    'utr' => $bankTxnId ?? $txnId,
                    'gateway_order_id' => $txnId,
                    'completed_time' => date('Y-m-d H:i:s'),
                    'verify_source' => 'paytm_api',
                    'gateway_response' => json_encode($statusResponse)
                ]);

                return $this->respond([
                    'status' => 'success',
                    'message' => 'Payment successful',
                    'txn_id' => $bankTxnId ?? $txnId
                ]);
            } elseif ($status === 'TXN_FAILURE') {
                // Update payment as failed
                $paymentModel->update($payment['id'], [
                    'status' => 'failed',
                    'gateway_response' => json_encode($statusResponse)
                ]);

                return $this->respond([
                    'status' => 'error',
                    'error' => $statusResponse['RESPMSG'] ?? 'Payment failed'
                ]);
            }
        }

        // Still pending
        return $this->respond([
            'status' => 'pending',
            'message' => 'Waiting for payment'
        ]);
    }

    /**
     * Paytm Callback Handler
     * Receives response from Paytm after payment
     */
    public function callback()
    {
        $request = \Config\Services::request();

        // Paytm can send data via POST or GET
        $paytmResponse = $request->getPost() ?: $request->getGet();

        log_message('info', 'Paytm Callback Received: ' . json_encode($paytmResponse));

        $orderId = $paytmResponse['ORDERID'] ?? null;
        $checksumHash = $paytmResponse['CHECKSUMHASH'] ?? null;
        $status = $paytmResponse['STATUS'] ?? 'PENDING';
        $txnId = $paytmResponse['TXNID'] ?? null;
        $bankTxnId = $paytmResponse['BANKTXNID'] ?? null;
        $respMsg = $paytmResponse['RESPMSG'] ?? '';
        $txnAmount = $paytmResponse['TXNAMOUNT'] ?? null;

        if (!$orderId) {
            log_message('error', 'Paytm Callback: No ORDER_ID received');
            return redirect()->to(base_url('payment/failure'))->with('error', 'Invalid callback - No order ID');
        }

        // Verify checksum
        $config = $this->getPaytmConfig();
        $isValidChecksum = $this->verifyChecksum($paytmResponse, $checksumHash, $config->merchantKey);

        if (!$isValidChecksum) {
            log_message('error', 'Paytm: Invalid checksum for order ' . $orderId);
            log_message('error', 'Expected checksum verification failed. Response: ' . json_encode($paytmResponse));
            return redirect()->to(base_url('payment/failure?order_id=' . $orderId))
                ->with('error', 'Payment verification failed - Invalid checksum');
        }

        log_message('info', 'Paytm: Checksum verified successfully for order ' . $orderId);

        $paymentModel = new \App\Models\PaymentModel();
        $payment = $paymentModel->where('txn_id', $orderId)->first();

        if (!$payment) {
            log_message('error', 'Paytm Callback: Payment not found for order ' . $orderId);
            return redirect()->to(base_url('payment/failure'))
                ->with('error', 'Payment record not found');
        }

        log_message('info', 'Paytm Callback: Payment found. Current status: ' . $payment['status'] . ', Paytm status: ' . $status);

        // Update payment based on response
        if ($status === 'TXN_SUCCESS') {
            $updateData = [
                'status' => 'success',
                'utr' => $bankTxnId ?? $txnId,
                'gateway_order_id' => $txnId,
                'gateway_txn_id' => $bankTxnId,
                'completed_time' => date('Y-m-d H:i:s'),
                'verify_source' => 'paytm_callback',
                'gateway_response' => json_encode($paytmResponse),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $paymentModel->update($payment['id'], $updateData);

            log_message('info', 'Paytm: Payment marked as SUCCESS for order ' . $orderId . ' with UTR: ' . ($bankTxnId ?? $txnId));

            // Redirect to success page
            return redirect()->to(base_url('payment/paytm/success?order_id=' . $orderId))
                ->with('success', 'Payment completed successfully!');

        } elseif ($status === 'TXN_FAILURE') {
            $updateData = [
                'status' => 'failed',
                'failure_reason' => $respMsg,
                'gateway_response' => json_encode($paytmResponse),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $paymentModel->update($payment['id'], $updateData);

            log_message('error', 'Paytm: Payment FAILED for order ' . $orderId . '. Reason: ' . $respMsg);

            // Redirect to failure page
            return redirect()->to(base_url('payment/failure?order_id=' . $orderId))
                ->with('error', 'Payment failed: ' . $respMsg);

        } else {
            // Pending or other status
            $updateData = [
                'status' => 'pending',
                'gateway_response' => json_encode($paytmResponse),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $paymentModel->update($payment['id'], $updateData);

            log_message('info', 'Paytm: Payment PENDING for order ' . $orderId . '. Status: ' . $status);

            // Redirect to pending/checkout page
            return redirect()->to(base_url('payment/checkout?txn_id=' . $orderId))
                ->with('info', 'Payment is being processed...');
        }
    }

    /**
     * Query Paytm Transaction Status
     */
    private function queryPaytmStatus($orderId)
    {
        $config = $this->getPaytmConfig();
        $requestParams = [
            'MID' => $config->merchantId,
            'ORDERID' => $orderId,
        ];

        $checksum = $this->generateChecksum($requestParams, $config->merchantKey);
        $requestParams['CHECKSUMHASH'] = $checksum;

        $url = $config->getApiUrl('status');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParams));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        log_message('error', 'Paytm Status Query Error: ' . $response);
        return null;
    }

    /**
     * Generate Paytm Checksum
     */
    private function generateChecksum($params, $key)
    {
        ksort($params);
        $paramStr = '';
        foreach ($params as $k => $v) {
            if ($k !== 'CHECKSUMHASH') {
                $paramStr .= $k . '=' . $v . '&';
            }
        }
        $paramStr = rtrim($paramStr, '&');

        return hash_hmac('sha256', $paramStr, $key);
    }

    /**
     * Verify Paytm Checksum
     */
    private function verifyChecksum($params, $checksum, $key)
    {
        $generatedChecksum = $this->generateChecksum($params, $key);
        return hash_equals($generatedChecksum, $checksum);
    }
}
