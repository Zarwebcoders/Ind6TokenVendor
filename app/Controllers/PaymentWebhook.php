<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PaymentModel;

class PaymentWebhook extends Controller
{
    protected $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Handle webhook from payment gateway
     * This simulates automatic payment capture
     */
    public function handleWebhook()
    {
        // Get raw POST data
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        // Log webhook data
        log_message('info', 'Webhook received: ' . $rawData);

        // Validate webhook data
        if (!isset($data['transaction_id']) || !isset($data['status'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid webhook data'
            ])->setStatusCode(400);
        }

        $transactionId = $data['transaction_id'];
        $status = $data['status']; // 'success', 'failed', 'pending'
        $utr = $data['utr'] ?? null;
        $gatewayTxnId = $data['gateway_txn_id'] ?? null;

        // Find payment
        $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();

        if (!$payment) {
            log_message('error', 'Payment not found for transaction: ' . $transactionId);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment not found'
            ])->setStatusCode(404);
        }

        // Update payment status
        $updateData = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($utr) {
            $updateData['utr'] = $utr;
        }

        if ($gatewayTxnId) {
            $updateData['gateway_txn_id'] = $gatewayTxnId;
        }

        if ($status === 'success') {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        $this->paymentModel->update($payment['id'], $updateData);

        log_message('info', 'Payment updated: ' . $transactionId . ' - Status: ' . $status);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Webhook processed successfully'
        ]);
    }

    /**
     * Simulate payment success (for testing)
     * This endpoint simulates a successful payment
     */
    public function simulateSuccess()
    {
        $transactionId = $this->request->getPost('transaction_id');

        if (!$transactionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction ID required'
            ]);
        }

        // Find payment
        $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();

        if (!$payment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment not found'
            ]);
        }

        // Update to success
        $updateData = [
            'status' => 'success',
            'gateway_txn_id' => 'UTR' . time(),
            'utr' => 'UTR' . time(),
            'completed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->paymentModel->update($payment['id'], $updateData);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment marked as successful'
        ]);
    }

    /**
     * Simulate payment failure (for testing)
     */
    public function simulateFailure()
    {
        $transactionId = $this->request->getPost('transaction_id');

        if (!$transactionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaction ID required'
            ]);
        }

        // Find payment
        $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();

        if (!$payment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment not found'
            ]);
        }

        // Update to failed
        $updateData = [
            'status' => 'failed',
            'failure_reason' => 'Payment declined by user',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->paymentModel->update($payment['id'], $updateData);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment marked as failed'
        ]);
    }
}
