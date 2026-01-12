<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\PaymentModel;

class VmpeGatewayApi extends Controller
{
    protected $paymentModel;

    // VMPE Configuration
    private $apiUrl = 'https://payments.vmpe.in/fintech/api/payin/create_intent';
    private $queryUrl = 'https://payments.vmpe.in/fintech/api/payin/query';
    private $bearerToken = 'K6i4GOWCEvn69QZ8dCEgy9rRJFpw4yQD3WLnQRdb'; // API Key from VMPE Panel (Created: 12-Jan-2026)
    private $clientId = '121';
    private $clientSecret = 'AGeEUnn22TRCIXb1DSkAsW93xGUEkilysCjB0iJe';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->paymentModel = new PaymentModel();
    }


    /**
     * Initiate VMPE Payment
     */
    public function initiatePayment()
    {
        try {
            $json = $this->request->getJSON(true);

            log_message('info', 'VMPE Payment Request: ' . json_encode($json));

            // Validate input
            if (!isset($json['user']) || !isset($json['amount'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User ID and amount are required'
                ]);
            }

            $userId = $json['user'];
            $amount = $json['amount'];
            $paymentMethod = $json['payment_method'] ?? 'upi_intent';

            // Get user data from request (sent from the form)
            $userName = $json['name'] ?? null;
            $userEmail = $json['email'] ?? null;
            $userMobile = $json['mobile'] ?? null;

            if (empty($userName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'The name field is required.'
                ]);
            }

            // Generate unique order ID
            $orderId = 'VMPE' . time() . rand(1000, 9999);

            // Prepare API request data
            $requestData = [
                'api_user_id' => $this->clientId,
                'amount' => $amount,
                'callback_url' => base_url('api/vmpe/webhook'),
                'unique_txnid' => $orderId,
                'name' => $userName,
                'email' => $userEmail,
                'mobile' => $userMobile
            ];

            log_message('info', 'VMPE API Request Data: ' . json_encode($requestData));

            // Make API request using cURL
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->bearerToken,
                'Client-ID: ' . $this->clientId,
                'Client-Secret: ' . $this->clientSecret,
                'accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            log_message('info', 'VMPE API Response: ' . $response);
            log_message('info', 'VMPE HTTP Code: ' . $httpCode);

            if ($curlError) {
                log_message('error', 'VMPE cURL Error: ' . $curlError);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Connection error: ' . $curlError
                ]);
            }

            $responseData = json_decode($response, true);

            if ($httpCode === 200 && isset($responseData['status']) && ($responseData['status'] === 'true' || $responseData['status'] === true)) {
                // Save payment to database
                $this->createPaymentRequest($orderId, $userId, $amount, 'vmpe');

                return $this->response->setJSON([
                    'success' => true,
                    'payment_url' => $responseData['order_details']['deeplink'] ?? $responseData['qrString'] ?? $responseData['payment_url'] ?? '',
                    'intent' => true,
                    'paymentId' => $orderId,
                    'order_id' => $orderId,
                    'message' => 'Payment initiated successfully'
                ]);
            } else {
                $errorMessage = $responseData['msg'] ?? $responseData['message'] ?? 'Failed to create payment request';
                log_message('error', 'VMPE Payment Failed: ' . $errorMessage);

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'VMPE Payment Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle VMPE Webhook
     */
    public function handleWebhook()
    {
        try {
            $payload = $this->request->getJSON(true);

            log_message('info', 'VMPE Webhook Received: ' . json_encode($payload));

            // Validate webhook data
            if (!isset($payload['status'])) {
                log_message('error', 'VMPE Webhook: Invalid payload - no status');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payload'
                ]);
            }

            $status = $payload['status'];

            if ($status !== 'success') {
                log_message('error', 'VMPE Webhook: Status is not success - ' . $status);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not successful'
                ]);
            }

            if (!isset($payload['order_id'])) {
                log_message('error', 'VMPE Webhook: No order_id in payload');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payload - no order_id'
                ]);
            }

            $orderId = $payload['order_id'];
            $utr = $payload['utr'] ?? $payload['txn_id'] ?? null;

            // Update payment status
            return $this->paymentSuccess($orderId, $utr);

        } catch (\Exception $e) {
            log_message('error', 'VMPE Webhook Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to process webhook',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check Payment Status
     */
    public function checkStatus()
    {
        try {
            if ($this->paymentModel === null) {
                $this->paymentModel = new PaymentModel();
            }

            $json = $this->request->getJSON(true);
            $orderId = $json['order_id'] ?? null;

            if (!$orderId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Order ID is required'
                ]);
            }

            // 1. Get payment from database
            $payment = $this->paymentModel->where('txn_id', $orderId)
                ->orWhere('platform_txn_id', $orderId)
                ->first();

            if (!$payment) {
                return $this->response->setJSON([
                    'success' => false,
                    'status' => 'pending',
                    'message' => 'Payment not found in local database'
                ]);
            }

            // 2. Call VMPE API to check real-time status
            $requestData = [
                'unique_txnid' => $orderId
            ];

            $ch = curl_init($this->queryUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->bearerToken,
                'accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            log_message('info', 'VMPE Status Query Response for ' . $orderId . ': ' . $response);

            if ($httpCode === 200) {
                $responseData = json_decode($response, true);
                $status = $responseData['status'] ?? 'pending';

                // If success, update local database
                if ($status === 'success' || $status === 'completed') {
                    $this->paymentModel->update($payment['id'], [
                        'status' => 'success',
                        'gateway_txn_id' => $responseData['utr'] ?? $responseData['txn_id'] ?? null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    return $this->response->setJSON([
                        'success' => true,
                        'status' => 'success',
                        'message' => 'Payment verified successfully from gateway',
                        'utr' => $responseData['utr'] ?? $responseData['txn_id'] ?? null,
                        'amount' => $payment['amount']
                    ]);
                } elseif ($status === 'failed' || $status === 'failure') {
                    $this->paymentModel->update($payment['id'], [
                        'status' => 'failed',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            // 3. Fallback to local database status if API check didn't result in success
            $payment = $this->paymentModel->find($payment['id']); // Refresh from DB

            // Map status
            $statusMap = [
                'pending' => 'pending',
                'initiated' => 'pending',
                'processing' => 'pending',
                'success' => 'success',
                'completed' => 'success',
                'failed' => 'failed',
                'declined' => 'failed',
                'expired' => 'failed'
            ];

            $responseStatus = $statusMap[$payment['status']] ?? 'pending';

            return $this->response->setJSON([
                'success' => true,
                'status' => $responseStatus,
                'payment_status' => $payment['status'],
                'utr' => $payment['gateway_txn_id'] ?? $payment['utr'] ?? null,
                'amount' => $payment['amount'],
                'message' => $this->getStatusMessage($payment['status'])
            ]);

        } catch (\Exception $e) {
            log_message('error', 'VMPE Status Check Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error checking status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create payment request in database
     */
    private function createPaymentRequest($orderId, $userId, $amount, $gateway)
    {
        try {
            if ($this->paymentModel === null) {
                $this->paymentModel = new PaymentModel();
            }
            $data = [
                'txn_id' => $orderId,
                'platform_txn_id' => $orderId,
                'vendor_id' => 1, // Default vendor
                'user_id' => $userId,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'pending',
                'gateway_name' => $gateway,
                'method' => 'upi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->paymentModel->insert($data);
            log_message('info', 'Payment request created: ' . $orderId);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to create payment request: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark payment as successful
     */
    private function paymentSuccess($orderId, $utr)
    {
        try {
            $payment = $this->paymentModel->where('txn_id', $orderId)
                ->orWhere('platform_txn_id', $orderId)
                ->first();

            if (!$payment) {
                log_message('error', 'Payment not found for success update: ' . $orderId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not found'
                ]);
            }

            // Update payment status
            $this->paymentModel->update($payment['id'], [
                'status' => 'success',
                'gateway_txn_id' => $utr,
                'utr' => $utr,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            log_message('info', 'Payment marked as successful: ' . $orderId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment successful',
                'order_id' => $orderId,
                'utr' => $utr
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Payment success update failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update payment status'
            ]);
        }
    }

    /**
     * Helper: Generate random email
     */
    private function generateRandomEmail()
    {
        return 'user' . time() . rand(100, 999) . '@example.com';
    }

    /**
     * Helper: Generate random name
     */
    private function generateRandomName()
    {
        $names = ['Rahul', 'Priya', 'Amit', 'Sneha', 'Vikram', 'Anjali', 'Rohan', 'Pooja'];
        return $names[array_rand($names)] . ' ' . rand(100, 999);
    }

    /**
     * Helper: Generate random mobile
     */
    private function generateRandomMobile()
    {
        return '9' . rand(100000000, 999999999);
    }

    /**
     * Get status message
     */
    private function getStatusMessage($status)
    {
        $messages = [
            'pending' => 'Waiting for payment...',
            'initiated' => 'Payment initiated, waiting for confirmation...',
            'processing' => 'Processing your payment...',
            'success' => 'Payment completed successfully!',
            'completed' => 'Payment completed successfully!',
            'failed' => 'Payment failed. Please try again.',
            'declined' => 'Payment was declined.',
            'expired' => 'Payment session expired.'
        ];

        return $messages[$status] ?? 'Unknown status';
    }
}
