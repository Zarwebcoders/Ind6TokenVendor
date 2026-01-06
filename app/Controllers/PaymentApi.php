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
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'txn_id' => $txnId,
            'status' => 'pending',
            'method' => 'gateway_api',
            'created_at' => date('Y-m-d H:i:s')
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
        $fmtAmount = number_format((float) $amount, 2, '.', '');

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
            'status' => 'initiated',
            'transaction_id' => $txnId,
            'payment_info' => [
                'amount' => $amount,
                'currency' => 'INR',
                'payee_vpa' => $upiId
            ],
            'bank_details' => $bankDetails,
            'upi_intent' => $intentLink,
            'paytm_intent' => $paytmLink,  // Paytm-specific deep link
            'upi_qr_string' => $qrLink
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
            'status' => $status,
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

        $input = $request->getJSON(true);

        // Try to get vendor_id from input first, then session
        $vendorId = $input['vendor_id'] ?? session()->get('id');

        if (!$vendorId) {
            return $this->failUnauthorized('Vendor authentication failed. Please provide vendor_id in request body.');
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
        $merchantId = '25'; // Your actual merchant ID
        $token = 'oE39Gq3Gkcv2gTvz8hePLi3cG4KVbc0Q2pkg4B5i'; // Your actual API token

        // Generate internal transaction ID
        $txnId = 'TXN_' . strtoupper(uniqid());

        // Create pending payment record
        $paymentModel = new \App\Models\PaymentModel();
        $paymentData = [
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'txn_id' => $txnId,
            'status' => 'pending',
            'method' => 'payraizen_gateway',
            'gateway_name' => 'payraizen',
            'created_at' => date('Y-m-d H:i:s')
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
            'Authorization: ' . $token,  // Payraizen expects token directly, no "Bearer" prefix
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

        // Add timeout settings to prevent long hangs
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); // 15 seconds to connect (reduced from 30)
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 seconds total execution time (reduced from 60)

        // Force IPv4 resolution (fixes many MAMP connection issues)
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        // DNS cache timeout
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);

        // Enable TCP keepalive
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
        curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 120);
        curl_setopt($ch, CURLOPT_TCP_KEEPINTVL, 60);

        // SSL settings - Environment aware
        // Production: Verify SSL for security
        // Local/Development: Can disable via environment variable if needed
        $verifySSL = getenv('PAYRAIZEN_VERIFY_SSL') !== 'false'; // Default: true (secure)

        if ($verifySSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            // Try to use system CA bundle or specify custom path
            $caBundlePath = getenv('CURL_CA_BUNDLE');
            if (!$caBundlePath || !file_exists($caBundlePath)) {
                // Try common macOS CA bundle locations
                $possiblePaths = [
                    '/etc/ssl/cert.pem',
                    '/usr/local/etc/openssl/cert.pem',
                    '/usr/local/etc/openssl@1.1/cert.pem',
                    '/Applications/MAMP/Library/OpenSSL/certs/cacert.pem'
                ];

                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $caBundlePath = $path;
                        break;
                    }
                }
            }

            if ($caBundlePath && file_exists($caBundlePath)) {
                curl_setopt($ch, CURLOPT_CAINFO, $caBundlePath);
                log_message('info', 'Using CA bundle: ' . $caBundlePath);
            }
        } else {
            // Only for local development/testing
            log_message('warning', 'PayRaizen API: SSL verification is DISABLED. This should only be used in development!');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        // Follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

        // Set user agent
        curl_setopt($ch, CURLOPT_USERAGENT, 'Ind6TokenVendor/1.0');

        // Log the request for debugging
        log_message('info', 'Payraizen API Request - URL: ' . $url . ', Data: ' . json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        // Check for cURL errors
        if ($curlError) {
            log_message('error', 'Payraizen cURL Error: ' . $curlError . ' (Error Code: ' . $curlErrno . ')');
            log_message('error', 'cURL Info: ' . json_encode($curlInfo));

            // Update payment status to failed
            try {
                $payment = $paymentModel->where('txn_id', $txnId)->first();
                $paymentModel->update($payment['id'], [
                    'status' => 'failed',
                    'gateway_response' => json_encode([
                        'curl_error' => $curlError,
                        'curl_errno' => $curlErrno,
                        'curl_info' => $curlInfo
                    ])
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Failed to update payment status: ' . $e->getMessage());
            }

            return $this->fail([
                'message' => 'Failed to connect to payment gateway',
                'error' => $curlError,
                'error_code' => $curlErrno,
                'debug_info' => [
                    'api_url' => $url,
                    'transaction_id' => $txnId,
                    'connection_info' => [
                        'total_time' => $curlInfo['total_time'] ?? 'N/A',
                        'namelookup_time' => $curlInfo['namelookup_time'] ?? 'N/A',
                        'connect_time' => $curlInfo['connect_time'] ?? 'N/A',
                    ]
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
            // Get raw input for logging
            $rawInput = file_get_contents('php://input');
            log_message('info', 'Payraizen Webhook Raw Input: ' . $rawInput);

            // Get the JSON payload from the request
            $payload = $request->getJSON(true);

            // Also try to get from POST if JSON is empty
            if (empty($payload)) {
                $payload = $request->getPost();
                log_message('info', 'Payraizen Webhook using POST data instead of JSON');
            }

            log_message('info', 'Payraizen Webhook Received ' . json_encode([
                'payload' => $payload,
                'request' => $request->getPost()
            ]));

            // Handle nested payload structure (sometimes Payraizen sends nested data)
            $orderDetails = null;

            // Check if order_details exists directly
            if (isset($payload['order_details'])) {
                $orderDetails = $payload['order_details'];
            }
            // Check if the entire payload IS the order_details
            elseif (isset($payload['tid']) && isset($payload['bank_utr'])) {
                $orderDetails = $payload;
            }
            // Check if payload is wrapped in another layer
            elseif (isset($payload['payload']['order_details'])) {
                $orderDetails = $payload['payload']['order_details'];
            }

            // Validate we found order details
            if (!$orderDetails) {
                log_message('error', 'Payraizen Webhook Error: Missing order_details. Payload: ' . json_encode($payload));
                // Still respond with success to prevent retries
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Invalid payload: Missing order_details'
                ]);
            }

            $status = $orderDetails['status'] ?? null;

            // Validate required fields
            if (!isset($orderDetails['tid'])) {
                log_message('error', 'Payraizen Webhook Error: Missing tid. Order Details: ' . json_encode($orderDetails));
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Invalid payload: Missing tid'
                ]);
            }

            $gatewayOrderId = $orderDetails['tid'];
            $bankUtr = $orderDetails['bank_utr'] ?? 'N/A';
            $amount = $orderDetails['amount'] ?? 0;

            log_message('info', 'Payraizen Webhook Processing: TID=' . $gatewayOrderId . ', Status=' . $status . ', UTR=' . $bankUtr);

            // Find payment by gateway order ID
            $paymentModel = new \App\Models\PaymentModel();
            $payment = $paymentModel->where('gateway_order_id', $gatewayOrderId)->first();

            if (!$payment) {
                log_message('error', 'Payraizen Webhook Error: Payment not found for gateway order ID: ' . $gatewayOrderId);

                // Try to find by amount and recent timestamp (within last 30 minutes)
                $recentTime = date('Y-m-d H:i:s', strtotime('-30 minutes'));
                $payment = $paymentModel
                    ->where('amount', $amount)
                    ->where('gateway_name', 'payraizen')
                    ->where('created_at >=', $recentTime)
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'DESC')
                    ->first();

                if ($payment) {
                    log_message('info', 'Payraizen Webhook: Found payment by amount matching - TXN: ' . $payment['txn_id']);
                    // Update the gateway_order_id for future reference
                    $paymentModel->update($payment['id'], ['gateway_order_id' => $gatewayOrderId]);
                } else {
                    log_message('error', 'Payraizen Webhook: No matching payment found by amount either');
                    // Still respond with success to prevent webhook retries
                    return $this->respond([
                        'status' => 'accepted',
                        'message' => 'Payment not found but webhook acknowledged'
                    ]);
                }
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
            } elseif ($status === 'failed' || $status === 'failure') {
                $updateData['status'] = 'failed';
            } else {
                // Unknown status, log it but don't update
                log_message('warning', 'Payraizen Webhook: Unknown status received: ' . $status);
            }

            try {
                $paymentModel->update($payment['id'], $updateData);

                log_message('info', 'Payraizen Webhook: Payment updated successfully - TXN: ' . $payment['txn_id'] . ', Status: ' . ($updateData['status'] ?? 'unknown') . ', UTR: ' . $bankUtr);

                // Respond quickly to prevent timeout
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Payment status updated successfully',
                    'transaction_id' => $payment['txn_id']
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Payraizen Webhook Error: Database update failed - ' . $e->getMessage());
                // Still respond with success to prevent retries
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Database error but webhook acknowledged',
                    'error' => $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Payraizen Webhook Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            // Always respond quickly to prevent timeout
            return $this->respond([
                'status' => 'error',
                'message' => 'Webhook acknowledged with error',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create Payraizen Payout Request
     * Initiates payout (withdrawal) through Payraizen gateway
     */
    public function createPayraizenPayout()
    {
        $request = \Config\Services::request();

        $input = $request->getJSON(true);

        // Try to get vendor_id from input first, then session
        $vendorId = $input['vendor_id'] ?? session()->get('id');

        if (!$vendorId) {
            return $this->failUnauthorized('Vendor authentication failed. Please provide vendor_id in request body.');
        }

        // Validate required fields
        $amount = $input['amount'] ?? 0;
        $beneficiaryName = $input['beneficiary_name'] ?? null;
        $accountNumber = $input['account_number'] ?? null;
        $ifscCode = $input['ifsc_code'] ?? null;
        $bankName = $input['bank_name'] ?? null;

        if ($amount <= 0) {
            return $this->fail('Invalid amount.');
        }

        if (!$beneficiaryName || !$accountNumber || !$ifscCode) {
            return $this->fail('Beneficiary details are required (name, account number, IFSC code).');
        }

        // Get vendor details
        $vendorModel = new \App\Models\VendorModel();
        $vendor = $vendorModel->find($vendorId);

        if (!$vendor) {
            return $this->failNotFound('Vendor not found.');
        }

        // Check if vendor has sufficient balance (optional - implement based on your business logic)
        // if ($vendor['balance'] < $amount) {
        //     return $this->fail('Insufficient balance.');
        // }

        // Payraizen credentials
        $merchantId = '25'; // Your actual merchant ID
        $token = 'oE39Gq3Gkcv2gTvz8hePLi3cG4KVbc0Q2pkg4B5i'; // Your actual API token

        // Generate internal transaction ID
        $txnId = 'PAYOUT_' . strtoupper(uniqid());

        // Create pending payout record
        $payoutModel = new \App\Models\PayoutModel();
        $payoutData = [
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'txn_id' => $txnId,
            'beneficiary_name' => $beneficiaryName,
            'account_number' => $accountNumber,
            'ifsc_code' => $ifscCode,
            'bank_name' => $bankName,
            'status' => 'pending',
            'method' => 'payraizen_payout',
            'gateway_name' => 'payraizen',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $payoutModel->insert($payoutData);
        } catch (\Exception $e) {
            return $this->failServerError('Database error: ' . $e->getMessage());
        }

        // Prepare Payraizen Payout API request
        $url = 'https://partner.payraizen.com/tech/api/payout/create';
        $data = [
            'beneficiary_name' => $beneficiaryName,
            'account_number' => $accountNumber,
            'ifsc_code' => $ifscCode,
            'bank_name' => $bankName ?? 'Unknown',
            'amount' => $amount,
            'mid' => $merchantId,
            'txn_id' => $txnId
        ];

        $headers = [
            'Authorization: ' . $token,  // Payraizen expects token directly, no "Bearer" prefix
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

        // Timeout settings
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Force IPv4
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        // SSL settings
        $verifySSL = getenv('PAYRAIZEN_VERIFY_SSL') !== 'false';
        if ($verifySSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        log_message('info', 'Payraizen Payout Request - URL: ' . $url . ', Data: ' . json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        // Check for cURL errors
        if ($curlError) {
            log_message('error', 'Payraizen Payout cURL Error: ' . $curlError);

            // Update payout status to failed
            try {
                $payout = $payoutModel->where('txn_id', $txnId)->first();
                $payoutModel->update($payout['id'], [
                    'status' => 'failed',
                    'failure_reason' => $curlError,
                    'gateway_response' => json_encode(['curl_error' => $curlError, 'curl_errno' => $curlErrno])
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Failed to update payout status: ' . $e->getMessage());
            }

            return $this->fail([
                'message' => 'Failed to connect to payout gateway',
                'error' => $curlError
            ], 500);
        }

        log_message('info', 'Payraizen Payout Response: ' . $response);

        $responseData = json_decode($response, true);

        if (isset($responseData['status']) && $responseData['status'] === 'true') {
            $gatewayOrderId = $responseData['order_details']['tid'] ?? null;

            // Update payout record with gateway order ID
            try {
                $payout = $payoutModel->where('txn_id', $txnId)->first();
                $payoutModel->update($payout['id'], [
                    'gateway_order_id' => $gatewayOrderId,
                    'gateway_response' => json_encode($responseData),
                    'status' => 'processing'
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Failed to update payout with gateway order ID: ' . $e->getMessage());
            }

            return $this->respond([
                'success' => true,
                'status' => 'processing',
                'transaction_id' => $txnId,
                'gateway_order_id' => $gatewayOrderId,
                'message' => 'Payout initiated successfully',
                'payout_info' => [
                    'amount' => $amount,
                    'beneficiary_name' => $beneficiaryName,
                    'account_number' => substr($accountNumber, -4) // Only show last 4 digits
                ]
            ]);
        } else {
            // Update payout status to failed
            try {
                $payout = $payoutModel->where('txn_id', $txnId)->first();
                $payoutModel->update($payout['id'], [
                    'status' => 'failed',
                    'failure_reason' => $responseData['msg'] ?? 'Unknown error',
                    'gateway_response' => json_encode($responseData)
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Failed to update payout status: ' . $e->getMessage());
            }

            $errorMessage = $responseData['msg'] ?? $responseData['message'] ?? 'Payout initiation failed.';

            return $this->fail([
                'message' => $errorMessage,
                'gateway_error' => $responseData
            ], $httpCode >= 400 ? $httpCode : 400);
        }
    }

    /**
     * Handle Payraizen Payout Webhook
     * Processes payout status updates from Payraizen
     */
    public function handlePayraizenPayoutWebhook()
    {
        $request = \Config\Services::request();

        try {
            // Get raw input for logging
            $rawInput = file_get_contents('php://input');
            log_message('info', 'Payraizen Payout Webhook Raw Input: ' . $rawInput);

            // Get the JSON payload
            $payload = $request->getJSON(true);

            if (empty($payload)) {
                $payload = $request->getPost();
                log_message('info', 'Payraizen Payout Webhook using POST data instead of JSON');
            }

            log_message('info', 'Payraizen Payout Webhook Received ' . json_encode($payload));

            // Handle nested payload structure
            $orderDetails = null;

            if (isset($payload['order_details'])) {
                $orderDetails = $payload['order_details'];
            } elseif (isset($payload['tid'])) {
                $orderDetails = $payload;
            } elseif (isset($payload['payload']['order_details'])) {
                $orderDetails = $payload['payload']['order_details'];
            }

            if (!$orderDetails) {
                log_message('error', 'Payraizen Payout Webhook Error: Missing order_details');
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Invalid payload: Missing order_details'
                ]);
            }

            $status = $orderDetails['status'] ?? null;

            if (!isset($orderDetails['tid'])) {
                log_message('error', 'Payraizen Payout Webhook Error: Missing tid');
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Invalid payload: Missing tid'
                ]);
            }

            $gatewayOrderId = $orderDetails['tid'];
            $bankUtr = $orderDetails['bank_utr'] ?? $orderDetails['utr'] ?? 'N/A';
            $amount = $orderDetails['amount'] ?? 0;

            log_message('info', 'Payraizen Payout Webhook Processing: TID=' . $gatewayOrderId . ', Status=' . $status . ', UTR=' . $bankUtr);

            // Find payout by gateway order ID
            $payoutModel = new \App\Models\PayoutModel();
            $payout = $payoutModel->where('gateway_order_id', $gatewayOrderId)->first();

            if (!$payout) {
                log_message('error', 'Payraizen Payout Webhook Error: Payout not found for gateway order ID: ' . $gatewayOrderId);

                // Try to find by amount and recent timestamp
                $recentTime = date('Y-m-d H:i:s', strtotime('-30 minutes'));
                $payout = $payoutModel
                    ->where('amount', $amount)
                    ->where('gateway_name', 'payraizen')
                    ->where('created_at >=', $recentTime)
                    ->where('status', 'processing')
                    ->orderBy('created_at', 'DESC')
                    ->first();

                if ($payout) {
                    log_message('info', 'Payraizen Payout Webhook: Found payout by amount matching - TXN: ' . $payout['txn_id']);
                    $payoutModel->update($payout['id'], ['gateway_order_id' => $gatewayOrderId]);
                } else {
                    log_message('error', 'Payraizen Payout Webhook: No matching payout found');
                    return $this->respond([
                        'status' => 'accepted',
                        'message' => 'Payout not found but webhook acknowledged'
                    ]);
                }
            }

            // Update payout status based on webhook
            $updateData = [
                'gateway_response' => json_encode($payload),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($status === 'success' || $status === 'completed') {
                $updateData['status'] = 'completed';
                $updateData['utr'] = $bankUtr;
                $updateData['completed_at'] = date('Y-m-d H:i:s');
                $updateData['verify_source'] = 'payraizen_webhook';

                // Optional: Update vendor balance (deduct the payout amount)
                // $vendorModel = new \App\Models\VendorModel();
                // $vendorModel->decrement('balance', $payout['vendor_id'], $amount);

            } elseif ($status === 'failed' || $status === 'failure') {
                $updateData['status'] = 'failed';
                $updateData['failure_reason'] = $orderDetails['failure_reason'] ?? 'Payout failed';

                // Optional: Refund vendor balance if it was deducted
                // $vendorModel = new \App\Models\VendorModel();
                // $vendorModel->increment('balance', $payout['vendor_id'], $amount);

            } else {
                log_message('warning', 'Payraizen Payout Webhook: Unknown status received: ' . $status);
            }

            try {
                $payoutModel->update($payout['id'], $updateData);

                log_message('info', 'Payraizen Payout Webhook: Payout updated successfully - TXN: ' . $payout['txn_id'] . ', Status: ' . ($updateData['status'] ?? 'unknown') . ', UTR: ' . $bankUtr);

                return $this->respond([
                    'status' => 'success',
                    'message' => 'Payout status updated successfully',
                    'transaction_id' => $payout['txn_id']
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Payraizen Payout Webhook Error: Database update failed - ' . $e->getMessage());
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Database error but webhook acknowledged',
                    'error' => $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Payraizen Payout Webhook Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $this->respond([
                'status' => 'error',
                'message' => 'Webhook acknowledged with error',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Test Payraizen Webhook
     * Debugging endpoint to test webhook functionality
     */
    public function testPayraizenWebhook()
    {
        $paymentModel = new \App\Models\PaymentModel();

        // Get recent Payraizen payments
        $recentPayments = $paymentModel
            ->where('gateway_name', 'payraizen')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        $output = [
            'status' => 'test',
            'message' => 'Payraizen Webhook Test Endpoint',
            'webhook_url' => base_url('api/payment/payraizen/webhook'),
            'recent_payments' => $recentPayments,
            'test_payload' => [
                'status' => 'true',
                'msg' => 'Payin Webhook',
                'order_details' => [
                    'amount' => 516,
                    'bank_utr' => 'TEST_UTR_' . time(),
                    'status' => 'success',
                    'tid' => $recentPayments[0]['gateway_order_id'] ?? 'NO_RECENT_PAYMENT',
                    'mid' => 'lysCjB0iJe',
                    'payee_vpa' => 'UPI'
                ]
            ],
            'curl_command' => "curl -X POST " . base_url('api/payment/payraizen/webhook') . " \\\n" .
                "  -H 'Content-Type: application/json' \\\n" .
                "  -d '" . json_encode([
                    'status' => 'true',
                    'msg' => 'Payin Webhook',
                    'order_details' => [
                        'amount' => 516,
                        'bank_utr' => 'TEST_UTR_' . time(),
                        'status' => 'success',
                        'tid' => $recentPayments[0]['gateway_order_id'] ?? 'NO_RECENT_PAYMENT',
                        'mid' => 'lysCjB0iJe',
                        'payee_vpa' => 'UPI'
                    ]
                ]) . "'"
        ];

        return $this->respond($output);
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
