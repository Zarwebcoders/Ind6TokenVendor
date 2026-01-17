<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\PaymentModel;

class Kay2PayGatewayApi extends Controller
{
    protected $paymentModel;

    // Kay2Pay Configuration
    private $apiUrl = 'https://kay2pay.in/api/v1/payin/create_intent';
    private $queryUrl = 'https://kay2pay.in/api/v1/payin/check_status';
    private $bearerToken = '0bu9HxJBxIhiHGoEuiwEKuv06W0NcP';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->paymentModel = new PaymentModel();
    }

    /**
     * Initiate Kay2Pay Payment
     */
    public function initiatePayment()
    {
        try {
            $json = $this->request->getJSON(true);

            log_message('info', 'Kay2Pay Payment Request: ' . json_encode($json));

            // Validate input
            if (!isset($json['user']) || !isset($json['amount'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User ID and amount are required'
                ]);
            }

            $userId = $json['user'];
            $amount = $json['amount'];

            // Get user data from request
            $userName = $json['name'] ?? 'Customer';
            $userEmail = $json['email'] ?? 'customer@example.com';
            $userMobile = $json['mobile'] ?? '9999999999';

            if (empty($userName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'The name field is required.'
                ]);
            }

            // Generate unique order ID
            $orderId = 'KAY' . time() . rand(1000, 9999);

            // Prepare API request data according to Kay2Pay docs
            $requestData = [
                'amount' => $amount,
                'udf1' => $orderId, // We use udf1 as our internal transaction ID
                'name' => $userName,
                'email' => $userEmail,
                'mobile' => $userMobile,
                'udf2' => $userId // Store userId in udf2 for later reference
            ];

            log_message('info', 'Kay2Pay API Request Data: ' . json_encode($requestData));

            // Make API request using cURL
            $ch = curl_init($this->apiUrl);
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
            $curlError = curl_error($ch);
            curl_close($ch);

            log_message('info', 'Kay2Pay API Response: ' . $response);
            log_message('info', 'Kay2Pay HTTP Code: ' . $httpCode);

            if ($curlError) {
                log_message('error', 'Kay2Pay cURL Error: ' . $curlError);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Connection error: ' . $curlError
                ]);
            }

            $responseData = json_decode($response, true);

            // Get the payment URL (UPI Intent/QR)
            $paymentUrl = $responseData['payment_url'] ?? $responseData['data']['payment_url'] ?? $responseData['qr_code'] ?? '';

            // Check status or success field from gateway
            $gatewayStatus = $responseData['status'] ?? $responseData['success'] ?? null;
            $isSuccessful = ($httpCode === 200 || $httpCode === 201) &&
                ($gatewayStatus === true || $gatewayStatus === 'true' || $gatewayStatus === 'success' || !empty($paymentUrl));

            // Check if payment was initiated successfully
            if ($isSuccessful) {

                // Save payment to database
                $this->createPaymentRequest($orderId, $userId, $amount, 'kay2pay', $paymentUrl, $responseData);

                return $this->response->setJSON([
                    'success' => true,
                    'payment_url' => $paymentUrl,
                    'intent' => true,
                    'paymentId' => $orderId,
                    'order_id' => $orderId,
                    'message' => 'Payment initiated successfully'
                ]);
            } else {
                $errorMessage = $responseData['msg'] ?? $responseData['message'] ?? 'Failed to create payment request';
                log_message('error', 'Kay2Pay Payment Failed: ' . $errorMessage);

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Kay2Pay Payment Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle Kay2Pay Webhook
     */
    public function handleWebhook()
    {
        try {
            $payload = $this->request->getJSON(true);

            log_message('info', 'Kay2Pay Webhook Received: ' . json_encode($payload));

            // Validate webhook data based on documentation finding:
            // { "event_name": "payin", "data": { "status": "success", "utr_no": "...", "txn_id": "...", "udf1": "..." } }
            if (!isset($payload['event_name']) || $payload['event_name'] !== 'payin' || !isset($payload['data'])) {
                log_message('error', 'Kay2Pay Webhook: Invalid payload format');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payload'
                ]);
            }

            $data = $payload['data'];
            $status = $data['status'] ?? 'failed';

            if ($status !== 'success') {
                log_message('error', 'Kay2Pay Webhook: Status is not success - ' . $status);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not successful'
                ]);
            }

            // udf1 contains our $orderId
            $orderId = $data['udf1'] ?? null;
            $utr = $data['utr_no'] ?? null;

            if (!$orderId) {
                log_message('error', 'Kay2Pay Webhook: No udf1 (order_id) in payload');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payload - no order_id'
                ]);
            }

            // Update payment status
            return $this->paymentSuccess($orderId, $utr);

        } catch (\Exception $e) {
            log_message('error', 'Kay2Pay Webhook Exception: ' . $e->getMessage());
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

            // 2. Call Kay2Pay API to check real-time status
            // Request body according to docs: { "check_by": "udf1", "check_value": "..." }
            $requestData = [
                'check_by' => 'udf1',
                'check_value' => $orderId
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

            log_message('info', 'Kay2Pay Status Check Response for ' . $orderId . ': ' . $response);

            if ($httpCode === 200) {
                $responseData = json_decode($response, true);

                // Usually Kay2Pay query returns { status: true, data: { status: 'success', ... } }
                $status = $responseData['data']['status'] ?? $responseData['status'] ?? $responseData['success'] ?? 'pending';

                if ($status === 'success' || $status === 'completed' || $status === true || $status === 'true') {
                    $utr = $responseData['data']['utr_no'] ?? $responseData['utr'] ?? null;

                    $this->paymentModel->update($payment['id'], [
                        'status' => 'success',
                        'gateway_txn_id' => $utr,
                        'utr' => $utr,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    return $this->response->setJSON([
                        'success' => true,
                        'status' => 'success',
                        'message' => 'Payment verified successfully from gateway',
                        'utr' => $utr,
                        'amount' => $payment['amount']
                    ]);
                } elseif ($status === 'failed' || $status === 'failure') {
                    $this->paymentModel->update($payment['id'], [
                        'status' => 'failed',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            // 3. Fallback to local database status
            $payment = $this->paymentModel->find($payment['id']); // Refresh from DB

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
            log_message('error', 'Kay2Pay Status Check Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error checking status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create payment request in database
     */
    private function createPaymentRequest($orderId, $userId, $amount, $gateway, $paymentUrl = null, $gatewayResponse = null)
    {
        try {
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
                'gateway_response' => is_array($gatewayResponse) ? json_encode($gatewayResponse) : $gatewayResponse,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->paymentModel->insert($data);
            log_message('info', 'Payment request created for Kay2Pay: ' . $orderId);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to create Kay2Pay payment request: ' . $e->getMessage());
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
                log_message('error', 'Kay2Pay Payment not found for success update: ' . $orderId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Payment not found'
                ]);
            }

            if ($payment['status'] === 'success') {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Payment already marked as success'
                ]);
            }

            // Update payment status
            $this->paymentModel->update($payment['id'], [
                'status' => 'success',
                'gateway_txn_id' => $utr,
                'utr' => $utr,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            log_message('info', 'Kay2Pay Payment marked as successful: ' . $orderId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment successful',
                'order_id' => $orderId,
                'utr' => $utr
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kay2Pay Payment success update failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update payment status'
            ]);
        }
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
