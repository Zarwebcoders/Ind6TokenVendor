<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PaymentModel;
use App\Models\VendorModel;

class PaymentTest extends Controller
{
    protected $paymentModel;
    protected $vendorModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->vendorModel = new VendorModel();
    }

    /**
     * Show test page
     */
    public function index()
    {
        return view('payment_test');
    }

    /**
     * Create test payment using PayRaizen gateway
     */
    public function createTestPayment()
    {
        $amount = $this->request->getPost('amount');
        $buyerName = $this->request->getPost('buyer_name');
        $buyerEmail = $this->request->getPost('buyer_email');
        $buyerPhone = $this->request->getPost('buyer_phone');
        $vendorId = $this->request->getPost('vendor_id');

        // Validate inputs
        if (!$amount || $amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid amount'
            ]);
        }

        // Check if vendor exists
        $vendor = $this->vendorModel->find($vendorId);
        if (!$vendor) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vendor not found'
            ]);
        }

        // PayRaizen credentials
        $merchantId = '987654321'; // Replace with your actual merchant ID
        $token = 'bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK'; // Replace with your actual token

        // Generate unique transaction ID
        $transactionId = 'TXN_' . strtoupper(uniqid());

        // Create pending payment record
        $paymentData = [
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'platform_txn_id' => $transactionId,
            'status' => 'pending',
            'buyer_name' => $buyerName ?: 'Test User',
            'buyer_email' => $buyerEmail ?: null,
            'buyer_phone' => $buyerPhone ?: null,
            'payment_method' => 'payraizen',
            'gateway_name' => 'payraizen',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->paymentModel->insert($paymentData);
        } catch (\Exception $e) {
            log_message('error', 'Failed to create test payment: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ]);
        }

        // Prepare PayRaizen API request
        $url = 'https://partner.payraizen.com/tech/api/payin/create_intent';
        $data = [
            'name' => $buyerName ?: 'Test User',
            'email' => $buyerEmail ?: 'test' . time() . '@example.com',
            'mobile' => $buyerPhone ?: '9' . rand(100000000, 999999999),
            'amount' => $amount,
            'mid' => $merchantId
        ];

        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'accept: application/json'
        ];

        // Make API call to PayRaizen
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
            log_message('error', 'PayRaizen cURL Error: ' . $curlError);

            // Update payment status to failed
            $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();
            $this->paymentModel->update($payment['id'], [
                'status' => 'failed',
                'gateway_response' => json_encode(['curl_error' => $curlError])
            ]);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to connect to payment gateway: ' . $curlError
            ]);
        }

        log_message('info', 'PayRaizen Response: ' . $response);

        $responseData = json_decode($response, true);

        if (isset($responseData['status']) && $responseData['status'] === 'true') {
            $gatewayOrderId = $responseData['order_details']['tid'];
            $deeplink = $responseData['order_details']['deeplink'];

            // Update payment record with gateway order ID
            $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();
            $this->paymentModel->update($payment['id'], [
                'gateway_order_id' => $gatewayOrderId,
                'gateway_response' => json_encode($responseData)
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment initiated successfully',
                'transaction_id' => $transactionId,
                'gateway_order_id' => $gatewayOrderId,
                'payment_url' => $deeplink,
                'checkout_url' => base_url('payment/checkout?txn_id=' . $transactionId)
            ]);
        } else {
            // PayRaizen API error
            $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();
            $this->paymentModel->update($payment['id'], [
                'status' => 'failed',
                'gateway_response' => $response
            ]);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment gateway error: ' . ($responseData['message'] ?? 'Unknown error'),
                'debug' => $responseData
            ]);
        }
    }

    /**
     * Create LocalPaisa payment request
     */
    public function createLocalPaisaRequest()
    {
        // LocalPaisa credentials
        $authorizedKey = 't15wECFgWNQoe+8cwkT/awPk+miH7e28fZaU51PjcXM0yzdT5PTFdw==';

        $amount = $this->request->getPost('amount');
        $buyerName = $this->request->getPost('buyer_name');
        $buyerEmail = $this->request->getPost('buyer_email');
        $buyerPhone = $this->request->getPost('buyer_phone');
        $vendorId = $this->request->getPost('vendor_id');

        // Validate inputs
        if (!$amount || $amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid amount'
            ]);
        }

        // Check if vendor exists
        $vendor = $this->vendorModel->find($vendorId);
        if (!$vendor) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vendor not found'
            ]);
        }

        // Generate unique order ID
        $orderId = 'LP_' . strtoupper(uniqid());

        // Prepare customer details
        $customerMobile = $buyerPhone ?: '9' . rand(100000000, 999999999);
        $customerEmail = $buyerEmail ?: $this->generateRandomEmail();
        $customerName = $buyerName ?: $this->generateRandomName();

        // Create pending payment record
        $paymentData = [
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'platform_txn_id' => $orderId,
            'status' => 'pending',
            'buyer_name' => $customerName,
            'buyer_email' => $customerEmail,
            'buyer_phone' => $customerMobile,
            'payment_method' => 'localpaisa',
            'gateway_name' => 'localpaisa',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->paymentModel->insert($paymentData);
        } catch (\Exception $e) {
            log_message('error', 'Failed to create LocalPaisa payment: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ]);
        }

        // Prepare LocalPaisa API request
        $url = 'http://api.localpaisa.com/api/Payment';
        $data = [
            'client_txn_id' => $orderId,
            'amount' => $amount,
            'customer_email' => $customerEmail,
            'customer_name' => $customerName,
            'customer_mobile' => $customerMobile,
            'redirect_url' => base_url('payment/localpaisa/webhook')
        ];

        // Log the request details
        log_message('info', 'Sending LocalPaisa Payin Request: ' . json_encode([
            'url' => $url,
            'data' => $data
        ]));

        $headers = [
            'AuthorizedKey: ' . $authorizedKey,
            'Content-Type: application/json'
        ];

        // Make API call to LocalPaisa
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
            log_message('error', 'LocalPaisa cURL Error: ' . $curlError);

            // Update payment status to failed
            $payment = $this->paymentModel->where('platform_txn_id', $orderId)->first();
            $this->paymentModel->update($payment['id'], [
                'status' => 'failed',
                'gateway_response' => json_encode(['curl_error' => $curlError])
            ]);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to connect to payment gateway: ' . $curlError
            ]);
        }

        log_message('info', 'LocalPaisa Payin Response: ' . $response);

        $responseData = json_decode($response, true);

        if (isset($responseData['statusCode']) && $responseData['statusCode'] === 'TXN') {
            $intentLink = $responseData['dataContent']['intent_link'] ?? null;
            $paymentLink = $responseData['dataContent']['payment_link'] ?? null;

            // Update payment record with gateway response
            $payment = $this->paymentModel->where('platform_txn_id', $orderId)->first();
            $this->paymentModel->update($payment['id'], [
                'gateway_response' => json_encode($responseData)
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment initiated successfully',
                'payment_url' => $intentLink ?: $paymentLink,
                'intent' => !empty($intentLink),
                'payment_id' => $orderId,
                'transaction_id' => $orderId
            ]);
        } else {
            // LocalPaisa API error
            $payment = $this->paymentModel->where('platform_txn_id', $orderId)->first();
            $this->paymentModel->update($payment['id'], [
                'status' => 'failed',
                'gateway_response' => $response
            ]);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create payment request: ' . ($responseData['message'] ?? 'Unknown error'),
                'debug' => $responseData
            ]);
        }
    }

    /**
     * Handle LocalPaisa webhook callback
     */
    public function handleLocalPaisaWebhook()
    {
        try {
            $payload = $this->request->getJSON(true);

            log_message('info', 'LocalPaisa Webhook Received: ' . json_encode([
                'payload' => $payload,
                'raw_input' => $this->request->getBody()
            ]));

            $events = $payload['events'] ?? null;

            if ($events === 'Payout') {
                // Handle payout event - you can implement payout logic here
                log_message('info', 'LocalPaisa Payout Event Received');

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Payout event received'
                ]);
            } elseif ($events === 'Upi_Transaction') {
                // Handle UPI transaction event
                $status = $payload['dataContent']['status'] ?? null;

                if ($status !== 'SUCCESS') {
                    log_message('error', 'LocalPaisa UPI Transaction Error: Status is not SUCCESS - ' . json_encode($payload));

                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Status is not SUCCESS'
                    ]);
                }

                if (!isset($payload['dataContent']['order_Id'])) {
                    log_message('error', 'LocalPaisa UPI Transaction Error: Invalid payload - ' . json_encode($payload));

                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid payload'
                    ]);
                }

                $orderId = $payload['dataContent']['order_Id'];
                $txnId = $payload['dataContent']['Transaction_Id'] ?? null;

                return $this->handlePaymentSuccess($orderId, $txnId);
            } else {
                log_message('error', 'LocalPaisa Webhook Error: Unknown event type - ' . json_encode($payload));

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unknown event type'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'LocalPaisa Webhook Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to process LocalPaisa webhook',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSuccess($orderId, $txnId = null)
    {
        try {
            // Find payment record
            $payment = $this->paymentModel->where('platform_txn_id', $orderId)->first();

            if (!$payment) {
                log_message('error', 'Payment not found for order ID: ' . $orderId);

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not found'
                ]);
            }

            // Update payment status
            $updateData = [
                'status' => 'completed',
                'gateway_txn_id' => $txnId,
                'completed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->paymentModel->update($payment['id'], $updateData);

            log_message('info', 'Payment completed successfully for order ID: ' . $orderId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment completed successfully',
                'order_id' => $orderId,
                'transaction_id' => $txnId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error processing payment success: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate random email
     */
    private function generateRandomEmail()
    {
        $domains = ['example.com', 'test.com', 'demo.com'];
        $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 10);
        $randomDomain = $domains[array_rand($domains)];

        return $randomString . '@' . $randomDomain;
    }

    /**
     * Generate random name
     */
    private function generateRandomName()
    {
        $firstNames = ['John', 'Jane', 'Alex', 'Sarah', 'Michael', 'Emily', 'David', 'Lisa'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];

        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];

        return $firstName . ' ' . $lastName;
    }
}
