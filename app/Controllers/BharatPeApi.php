<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class BharatPeApi extends ResourceController
{
    protected $format = 'json';
    private $config;

    /**
     * Constructor - Load BharatPe Configuration
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = config('BharatPe');
    }

    /**
     * Create BharatPe Payment Request
     * Generates a dynamic QR code and payment link
     */
    public function createPayment()
    {
        $request = \Config\Services::request();
        $input = $request->getJSON(true);

        $vendorId = session()->get('id') ?? $input['vendor_id'] ?? null;
        $amount = $input['amount'] ?? 0;

        if (!$vendorId || $amount <= 0) {
            return $this->fail('Invalid request');
        }

        // Generate unique order ID
        $orderId = 'BP_' . strtoupper(uniqid());

        // Create payment record
        $paymentModel = new \App\Models\PaymentModel();
        $paymentData = [
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'txn_id' => $orderId,
            'status' => 'pending',
            'method' => 'bharatpe_qr',
            'gateway_name' => 'bharatpe',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $paymentModel->insert($paymentData);
        } catch (\Exception $e) {
            return $this->failServerError('Database error: ' . $e->getMessage());
        }

        // Call BharatPe API to create payment request
        $bharatpeResponse = $this->callBharatPeAPI('/payments/create', [
            'order_id' => $orderId,
            'amount' => $amount,
            'merchant_id' => $this->config->merchantId,
            'callback_url' => $this->config->callbackUrl
        ]);

        if ($bharatpeResponse && isset($bharatpeResponse['qr_code'])) {
            // Update payment with BharatPe details
            $payment = $paymentModel->where('txn_id', $orderId)->first();
            $paymentModel->update($payment['id'], [
                'gateway_order_id' => $bharatpeResponse['payment_id'] ?? $orderId,
                'gateway_response' => json_encode($bharatpeResponse)
            ]);

            return $this->respond([
                'success' => true,
                'order_id' => $orderId,
                'qr_code' => $bharatpeResponse['qr_code'],
                'upi_intent' => $bharatpeResponse['upi_intent'] ?? null,
                'amount' => $amount
            ]);
        }

        return $this->fail('Failed to create BharatPe payment');
    }

    /**
     * Check Payment Status
     * Polls BharatPe API to check if payment is received
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
                'utr' => $payment['utr']
            ]);
        }

        // Call BharatPe API to check status
        $bharatpeResponse = $this->callBharatPeAPI('/payments/status', [
            'order_id' => $orderId,
            'merchant_id' => $this->config->merchantId
        ]);

        if ($bharatpeResponse) {
            $status = $bharatpeResponse['status'] ?? 'pending';
            $utr = $bharatpeResponse['utr'] ?? null;

            // Update payment status
            if ($status === 'SUCCESS' || $status === 'success') {
                $paymentModel->update($payment['id'], [
                    'status' => 'success',
                    'utr' => $utr,
                    'completed_time' => date('Y-m-d H:i:s'),
                    'verify_source' => 'bharatpe_api',
                    'gateway_response' => json_encode($bharatpeResponse)
                ]);

                return $this->respond([
                    'status' => 'success',
                    'message' => 'Payment successful',
                    'utr' => $utr
                ]);
            } elseif ($status === 'FAILED' || $status === 'failed') {
                $paymentModel->update($payment['id'], [
                    'status' => 'failed',
                    'gateway_response' => json_encode($bharatpeResponse)
                ]);

                return $this->respond([
                    'status' => 'error',
                    'error' => 'Payment failed'
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
     * BharatPe Webhook Callback
     * Receives automatic notifications from BharatPe
     */
    public function callback()
    {
        $request = \Config\Services::request();
        $payload = $request->getJSON(true);

        log_message('info', 'BharatPe Callback: ' . json_encode($payload));

        $orderId = $payload['order_id'] ?? null;
        $status = $payload['status'] ?? null;
        $utr = $payload['utr'] ?? null;

        if (!$orderId) {
            return $this->fail('Invalid callback');
        }

        $paymentModel = new \App\Models\PaymentModel();
        $payment = $paymentModel->where('txn_id', $orderId)->first();

        if (!$payment) {
            return $this->failNotFound('Payment not found');
        }

        // Update payment based on callback
        if ($status === 'SUCCESS') {
            $paymentModel->update($payment['id'], [
                'status' => 'success',
                'utr' => $utr,
                'completed_time' => date('Y-m-d H:i:s'),
                'verify_source' => 'bharatpe_webhook',
                'gateway_response' => json_encode($payload)
            ]);
        } elseif ($status === 'FAILED') {
            $paymentModel->update($payment['id'], [
                'status' => 'failed',
                'gateway_response' => json_encode($payload)
            ]);
        }

        return $this->respond(['status' => 'ok']);
    }

    /**
     * Helper: Call BharatPe API
     */
    private function callBharatPeAPI($endpoint, $data)
    {
        $url = $this->config->baseUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'X-API-Key: ' . $this->config->apiKey,
            'X-Merchant-ID: ' . $this->config->merchantId
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        log_message('error', 'BharatPe API Error: ' . $response);
        return null;
    }
}
