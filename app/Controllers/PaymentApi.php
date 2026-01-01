<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class PaymentApi extends ResourceController
{
    protected $format = 'json';

    /**
     * Initiate a payment via the Vendor Dashboard or Personal App.
     * Fetches Vendor Bank Details and creates a pending transaction.
     */
    public function initiate()
    {
        $request = \Config\Services::request();
        
        // Try to get vendor_id from session (Dashboard usage) or Input (App usage)
        $vendorId = session()->get('id'); 
        
        $input = $request->getJSON(true); // Get JSON input
        
        if (!$vendorId && isset($input['vendor_id'])) {
            $vendorId = $input['vendor_id'];
        }

        if (!$vendorId) {
            return $this->failUnauthorized('Vendor authentication failed.');
        }

        $amount = $input['amount'] ?? 0;

        if ($amount <= 0) {
            return $this->fail('Invalid amount.');
        }

        $bankModel = new \App\Models\BankDetailModel();
        $paymentModel = new \App\Models\PaymentModel();

        // 1. Get Bank Details
        // Modified: Fetch GLOBAL active bank details (not vendor specific) as per requirement.
        // We prioritize the 'default' one, or just the first active one found.
        $bankDetails = $bankModel->where('active', 1)
                                 ->orderBy('is_default', 'DESC') // specific preference
                                 ->orderBy('id', 'ASC')
                                 ->first();

        if (!$bankDetails) {
             return $this->fail('No active bank details found in the system. Please configure them in Utilities.', 400);
        }

        // 2. Generate Transaction ID
        $txnId = 'TXN_' . strtoupper(uniqid());

        // 3. Create Pending Record
        $paymentData = [
            'vendor_id'    => $vendorId,
            'amount'       => $amount,
            'txn_id'       => $txnId,
            'status'       => 'pending',
            'method'       => 'gateway_api',
            'created_at'   => date('Y-m-d H:i:s')
        ];

        try {
            $paymentModel->insert($paymentData);
        } catch (\Exception $e) {
            return $this->failServerError('Database error: ' . $e->getMessage());
        }

        // 4. Generate UPI Intent String (For App Deep Linking)
        // Format: upi://pay?pa={upi_id}&pn={name}&am={amount}&tr={txn_id}&cu=INR&tn={note}
        
        // UPI Configuration - Update these with your actual details
        $upiId = "abuzarmunshi12-2@okaxis"; // Your UPI ID
        $payeeName = "Zarwebcoders"; // Your business/account name
        
        // Paytm Merchant Configuration (Test Credentials)
        $paytmMerchantId = "hhdaGX54213527799734"; // Paytm Test Merchant ID
        $paytmMerchantKey = "1OLuORB&@k13LBXv"; // Paytm Test Merchant Key (Secret Key)
        $paytmMerchantCode = "4722"; // Merchant Category Code (MCC)
        
        // Format amount properly (2 decimal places)
        $fmtAmount = number_format((float)$amount, 2, '.', '');
        
        // Create payment note
        $note = "Payment for Order {$txnId}";
        
        // URL encode the name and note (but NOT the UPI ID)
        $encodedName = urlencode($payeeName);
        $encodedNote = urlencode($note);

        // Standard UPI Intent Link (works with all UPI apps: GPay, PhonePe, Paytm, etc.)
        // Format: upi://pay?pa=UPI_ID&pn=NAME&tr=TXN_ID&am=AMOUNT&cu=CURRENCY&tn=NOTE
        $intentLink = "upi://pay?pa={$upiId}&pn={$encodedName}&tr={$txnId}&am={$fmtAmount}&cu=INR&tn={$encodedNote}";
        
        // Paytm-Specific Deep Link (paytmmp://)
        // This format is specifically for Paytm app and includes merchant code and signature
        // Generate signature using Paytm Merchant Key
        $signatureData = "{$upiId}|{$payeeName}|{$fmtAmount}|{$txnId}|{$paytmMerchantKey}";
        $signature = base64_encode(hash('sha256', $signatureData, true));
        
        // Paytm deep link format with merchant ID
        $paytmLink = "paytmmp://cash_wallet?pa={$upiId}&pn={$encodedName}&am={$fmtAmount}&cu=INR&tn={$encodedNote}&tr={$txnId}&mc={$paytmMerchantCode}&mid={$paytmMerchantId}&sign={$signature}&featuretype=money_transfer";
        
        // Short Link (For QR) - MINIMAL to avoid QR code length overflow
        // Remove the note to keep it short enough for QR codes (max ~1000 chars)
        $qrLink = "upi://pay?pa={$upiId}&pn={$encodedName}&tr={$txnId}&am={$fmtAmount}&cu=INR";

        // 4. Return Data
        return $this->respond([
            'status'         => 'initiated',
            'transaction_id' => $txnId,
            'payment_info'   => [
                'amount' => $amount,
                'currency' => 'INR',
                'payee_vpa' => $upiId
            ],
            'bank_details'   => $bankDetails,
            'upi_intent'     => $intentLink,
            'paytm_intent'   => $paytmLink,  // Paytm-specific deep link
            'upi_qr_string'  => $qrLink
        ]);
    }

    /**
     * Update Payment Status
     * Called by the App or Gateway Webhook
     */
    public function updateStatus()
    {
        $request = \Config\Services::request();
        $input = $request->getJSON(true);

        $txnId = $input['transaction_id'] ?? null;
        $status = $input['status'] ?? null; // 'success', 'failed'
        $utr = $input['utr'] ?? null; // The bank UTR or Reference Number

        if (!$txnId || !$status) {
            return $this->fail('Transaction ID and Status are required.');
        }

        $paymentModel = new \App\Models\PaymentModel();
        $payment = $paymentModel->where('txn_id', $txnId)->first();

        if (!$payment) {
            return $this->failNotFound('Transaction not found.');
        }

        // Data to update
        $updateData = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // If UTR is provided, update it
        if ($utr) {
            $updateData['utr'] = $utr;
        }

        // Update record
        try {
            $paymentModel->update($payment['id'], $updateData);
        } catch (\Exception $e) {
             return $this->failServerError('Database error details: ' . $e->getMessage());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Transaction status and UTR updated successfully.'
        ]);
    }

    /**
     * Verify Payment
     * Verifies UPI payment response and updates transaction status
     */
    public function verifyPayment()
    {
        $request = \Config\Services::request();
        $input = $request->getJSON(true);

        $txnId = $input['transaction_id'] ?? null;
        $status = $input['status'] ?? null; // 'SUCCESS', 'FAILED'
        $approvalRefNo = $input['approval_ref_no'] ?? null; // UTR/RRN
        $responseCode = $input['response_code'] ?? null;
        $txnRefId = $input['txn_ref_id'] ?? null;

        if (!$txnId) {
            return $this->fail('Transaction ID is required.');
        }

        $paymentModel = new \App\Models\PaymentModel();
        $payment = $paymentModel->where('txn_id', $txnId)->first();

        if (!$payment) {
            return $this->failNotFound('Transaction not found.');
        }

        // Map UPI status to our status
        $mappedStatus = 'pending';
        if (strtoupper($status) === 'SUCCESS' && $responseCode === '00') {
            $mappedStatus = 'success';
        } elseif (strtoupper($status) === 'FAILED') {
            $mappedStatus = 'failed';
        }

        // Data to update
        $updateData = [
            'status' => $mappedStatus,
            'updated_at' => date('Y-m-d H:i:s'),
            'verify_source' => 'upi_response'
        ];

        // If UTR is provided, update it
        if ($approvalRefNo) {
            $updateData['utr'] = $approvalRefNo;
        }

        // If transaction was successful, mark completion time
        if ($mappedStatus === 'success') {
            $updateData['completed_time'] = date('Y-m-d H:i:s');
        }

        // Update record
        try {
            $paymentModel->update($payment['id'], $updateData);
        } catch (\Exception $e) {
            return $this->failServerError('Database error: ' . $e->getMessage());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Payment verified successfully.',
            'transaction_status' => $mappedStatus,
            'utr' => $approvalRefNo
        ]);
    }

    /**
     * Query Payment Status
     * Retrieves the current status of a payment transaction
     */
    public function queryStatus()
    {
        $request = \Config\Services::request();
        $input = $request->getJSON(true);

        $txnId = $input['transaction_id'] ?? null;

        if (!$txnId) {
            return $this->fail('Transaction ID is required.');
        }

        $paymentModel = new \App\Models\PaymentModel();
        $payment = $paymentModel->where('txn_id', $txnId)->first();

        if (!$payment) {
            return $this->failNotFound('Transaction not found.');
        }

        return $this->respond([
            'status' => 'success',
            'transaction_id' => $payment['txn_id'],
            'transaction_status' => $payment['status'],
            'amount' => $payment['amount'],
            'utr' => $payment['utr'] ?? null,
            'created_at' => $payment['created_at'],
            'updated_at' => $payment['updated_at'],
            'completed_time' => $payment['completed_time'] ?? null
        ]);
    }

    /**
     * Create Payraizen Payment Request
     * Initiates payment through Payraizen gateway
     */
    public function createPayraizenRequest()
    {
        $request = \Config\Services::request();
        
        // Try to get vendor_id from session (Dashboard usage) or Input (App usage)
        $vendorId = session()->get('id'); 
        
        $input = $request->getJSON(true);
        
        if (!$vendorId && isset($input['vendor_id'])) {
            $vendorId = $input['vendor_id'];
        }

        if (!$vendorId) {
            return $this->failUnauthorized('Vendor authentication failed.');
        }

        $amount = $input['amount'] ?? 0;

        if ($amount <= 0) {
            return $this->fail('Invalid amount.');
        }

        // Get vendor details
        $vendorModel = new \App\Models\VendorModel();
        $vendor = $vendorModel->find($vendorId);

        if (!$vendor) {
            return $this->failNotFound('Vendor not found.');
        }

        // Prepare vendor data for Payraizen
        $userMobile = $vendor['phone'] ?? $this->generateRandomMobile();
        $userEmail = !empty($vendor['email']) ? $vendor['email'] : $this->generateRandomEmail();
        $userName = !empty($vendor['name']) ? $vendor['name'] : $this->generateRandomName();

        // Payraizen credentials (should be moved to .env in production)
        $merchantId = '987654321'; // Replace with your actual merchant ID
        $token = 'bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK'; // Replace with your actual token

        // Generate internal transaction ID
        $txnId = 'TXN_' . strtoupper(uniqid());

        // Create pending payment record
        $paymentModel = new \App\Models\PaymentModel();
        $paymentData = [
            'vendor_id'    => $vendorId,
            'amount'       => $amount,
            'txn_id'       => $txnId,
            'status'       => 'pending',
            'method'       => 'payraizen_gateway',
            'gateway_name' => 'payraizen',
            'created_at'   => date('Y-m-d H:i:s')
        ];

        try {
            $paymentModel->insert($paymentData);
        } catch (\Exception $e) {
            return $this->failServerError('Database error: ' . $e->getMessage());
        }

        // Prepare Payraizen API request
        $url = 'https://partner.payraizen.com/tech/api/payin/create_intent';
        $data = [
            'name' => $userName,
            'email' => $userEmail,
            'mobile' => $userMobile,
            'amount' => $amount,
            'mid' => $merchantId
        ];

        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'accept: application/json'
        ];

        // Make API call to Payraizen
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Check for cURL errors
        if ($curlError) {
            log_message('error', 'Payraizen cURL Error: ' . $curlError);
            
            // Update payment status to failed
            try {
                $payment = $paymentModel->where('txn_id', $txnId)->first();
                $paymentModel->update($payment['id'], [
                    'status' => 'failed',
                    'gateway_response' => json_encode(['curl_error' => $curlError])
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Failed to update payment status: ' . $e->getMessage());
            }

            return $this->fail([
                'message' => 'Failed to connect to payment gateway',
                'error' => $curlError,
                'debug_info' => [
                    'api_url' => $url,
                    'transaction_id' => $txnId
                ]
            ], 500);
        }

        // Log the response
        log_message('info', 'Payraizen Response: ' . $response);

        $responseData = json_decode($response, true);

        if (isset($responseData['status']) && $responseData['status'] === 'true') {
            $gatewayOrderId = $responseData['order_details']['tid'];
            
            // Update payment record with gateway order ID
            try {
                $payment = $paymentModel->where('txn_id', $txnId)->first();
                $paymentModel->update($payment['id'], [
                    'gateway_order_id' => $gatewayOrderId,
                    'gateway_response' => json_encode($responseData)
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Failed to update payment with gateway order ID: ' . $e->getMessage());
            }

            return $this->respond([
                'success' => true,
                'status' => 'initiated',
                'transaction_id' => $txnId,
                'gateway_order_id' => $gatewayOrderId,
                'payment_url' => $responseData['order_details']['deeplink'],
                'intent' => true,
                'payment_info' => [
                    'amount' => $amount,
                    'currency' => 'INR'
                ]
            ]);
        } else {
            // Update payment status to failed
            try {
                $payment = $paymentModel->where('txn_id', $txnId)->first();
                $paymentModel->update($payment['id'], [
                    'status' => 'failed',
                    'gateway_response' => json_encode($responseData)
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Failed to update payment status: ' . $e->getMessage());
            }

            // Log detailed error for debugging
            log_message('error', 'Payraizen API Error - HTTP Code: ' . $httpCode . ', Response: ' . $response);

            // Return detailed error information
            $errorMessage = $responseData['msg'] ?? $responseData['message'] ?? 'Payment initiation failed.';
            
            return $this->fail([
                'message' => $errorMessage,
                'gateway_error' => $responseData,
                'http_code' => $httpCode,
                'debug_info' => [
                    'api_url' => $url,
                    'merchant_id' => $merchantId,
                    'transaction_id' => $txnId
                ]
            ], $httpCode >= 400 ? $httpCode : 400);
        }
    }

    /**
     * Handle Payraizen Webhook
     * Processes payment status updates from Payraizen
     */
    public function handlePayraizenWebhook()
    {
        $request = \Config\Services::request();
        
        try {
            // Get the JSON payload from the request
            $payload = $request->getJSON(true);

            log_message('info', 'Payraizen Webhook Received: ' . json_encode($payload));

            // Validate payload structure
            if (!isset($payload['order_details'])) {
                log_message('error', 'Payraizen Webhook Error: Missing order_details');
                return $this->fail('Invalid payload: Missing order_details');
            }

            $orderDetails = $payload['order_details'];
            $status = $orderDetails['status'] ?? null;

            // Validate required fields
            if (!isset($orderDetails['tid']) || !isset($orderDetails['bank_utr'])) {
                log_message('error', 'Payraizen Webhook Error: Missing tid or bank_utr');
                return $this->fail('Invalid payload: Missing required fields');
            }

            $gatewayOrderId = $orderDetails['tid'];
            $bankUtr = $orderDetails['bank_utr'];

            // Find payment by gateway order ID
            $paymentModel = new \App\Models\PaymentModel();
            $payment = $paymentModel->where('gateway_order_id', $gatewayOrderId)->first();

            if (!$payment) {
                log_message('error', 'Payraizen Webhook Error: Payment not found for gateway order ID: ' . $gatewayOrderId);
                return $this->failNotFound('Payment not found');
            }

            // Update payment status based on webhook
            $updateData = [
                'gateway_response' => json_encode($payload),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($status === 'success') {
                $updateData['status'] = 'success';
                $updateData['utr'] = $bankUtr;
                $updateData['completed_time'] = date('Y-m-d H:i:s');
                $updateData['verify_source'] = 'payraizen_webhook';
            } else {
                $updateData['status'] = 'failed';
            }

            try {
                $paymentModel->update($payment['id'], $updateData);
                
                log_message('info', 'Payraizen Webhook: Payment updated successfully - TXN: ' . $payment['txn_id'] . ', Status: ' . ($updateData['status'] ?? 'unknown'));

                return $this->respond([
                    'status' => 'success',
                    'message' => 'Payment status updated successfully',
                    'transaction_id' => $payment['txn_id']
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Payraizen Webhook Error: Database update failed - ' . $e->getMessage());
                return $this->failServerError('Database error: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            log_message('error', 'Payraizen Webhook Error: ' . $e->getMessage());
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to process Payraizen webhook',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Generate random email
     */
    private function generateRandomEmail()
    {
        return 'user' . time() . rand(1000, 9999) . '@example.com';
    }

    /**
     * Helper: Generate random name
     */
    private function generateRandomName()
    {
        return 'User' . rand(1000, 9999);
    }

    /**
     * Helper: Generate random mobile
     */
    private function generateRandomMobile()
    {
        return '9' . rand(100000000, 999999999);
    }
}
