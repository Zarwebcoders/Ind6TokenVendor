<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PaymentModel;
use App\Models\VendorModel;

class PaymentCheckout extends Controller
{
    protected $paymentModel;
    protected $vendorModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->vendorModel = new VendorModel();
    }

    /**
     * Display payment checkout page
     */
    public function index()
    {
        $transactionId = $this->request->getGet('txn_id');
        
        log_message('info', 'Checkout page accessed with txn_id: ' . ($transactionId ?? 'NULL'));
        
        if (!$transactionId) {
            log_message('error', 'No transaction ID provided');
            return redirect()->to('/payment/test')->with('error', 'Invalid payment request - No transaction ID');
        }

        // Get payment details
        $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();
        
        log_message('info', 'Payment lookup result: ' . ($payment ? 'Found' : 'Not found'));
        
        if (!$payment) {
            log_message('error', 'Payment not found for txn_id: ' . $transactionId);
            return redirect()->to('/payment/test')->with('error', 'Payment not found: ' . $transactionId);
        }

        // Check if payment is already completed
        if (in_array($payment['status'], ['success', 'completed'])) {
            return redirect()->to('/payment/success?txn=' . $transactionId);
        }

        if ($payment['status'] === 'failed') {
            return redirect()->to('/payment/failure?txn=' . $transactionId);
        }

        // Get vendor details
        $vendor = $this->vendorModel->find($payment['vendor_id']);
        
        log_message('info', 'Vendor lookup for ID ' . $payment['vendor_id'] . ': ' . ($vendor ? 'Found' : 'Not found'));
        
        if (!$vendor) {
            log_message('error', 'Vendor not found for ID: ' . $payment['vendor_id']);
            return redirect()->to('/payment/test')->with('error', 'Vendor not found');
        }

        // Generate QR code data (UPI deep link)
        $upiId = $vendor['upi_id'] ?? 'abuzarmunshi12-2@okaxis';
        $amount = $payment['amount'];
        $orderRef = $payment['platform_txn_id'];
        
        // Create UPI payment string
        $upiString = "upi://pay?pa={$upiId}&pn=" . urlencode($vendor['business_name'] ?? 'Merchant') . 
                     "&am={$amount}&cu=INR&tn=" . urlencode("Payment for Order {$orderRef}") . 
                     "&tr={$orderRef}";

        // Generate QR code URL (using QR Server API - free alternative)
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($upiString);

        // Prepare data for view
        $data = [
            'transaction_id' => $transactionId,
            'amount' => number_format($payment['amount'], 2),
            'order_id' => $payment['platform_txn_id'],
            'upi_id' => $upiId,
            'upi_string' => $upiString,
            'qr_code_url' => $qrCodeUrl,
            'merchant_name' => $vendor['business_name'] ?? 'Merchant',
            'created_at' => date('d M, Y h:i A', strtotime($payment['created_at'])),
            'buyer_name' => $payment['buyer_name'] ?? 'Customer',
            'buyer_email' => $payment['buyer_email'] ?? '',
            'buyer_phone' => $payment['buyer_phone'] ?? ''
        ];

        return view('payment_checkout', $data);
    }

    /**
     * Check payment status via AJAX
     */
    public function checkStatus()
    {
        $transactionId = $this->request->getPost('transaction_id');
        
        if (!$transactionId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaction ID required'
            ]);
        }

        // Get payment from database
        $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();
        
        if (!$payment) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaction not found'
            ]);
        }

        // Map database status to response status
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
            'status' => $responseStatus,
            'payment_status' => $payment['status'],
            'message' => $this->getStatusMessage($payment['status']),
            'utr' => $payment['gateway_txn_id'] ?? null,
            'amount' => $payment['amount']
        ]);
    }

    /**
     * Payment success page
     */
    public function success()
    {
        $transactionId = $this->request->getGet('txn');
        
        if (!$transactionId) {
            return redirect()->to('/');
        }

        $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();
        
        if (!$payment) {
            return redirect()->to('/')->with('error', 'Payment not found');
        }

        $data = [
            'transaction_id' => $transactionId,
            'amount' => number_format($payment['amount'], 2),
            'utr' => $payment['gateway_txn_id'] ?? 'N/A',
            'date' => date('d M, Y h:i A', strtotime($payment['updated_at'])),
            'status' => 'Success'
        ];

        return view('payment_success', $data);
    }

    /**
     * Payment failure page
     */
    public function failure()
    {
        $transactionId = $this->request->getGet('txn');
        
        if (!$transactionId) {
            return redirect()->to('/');
        }

        $payment = $this->paymentModel->where('platform_txn_id', $transactionId)->first();
        
        if (!$payment) {
            return redirect()->to('/')->with('error', 'Payment not found');
        }

        $data = [
            'transaction_id' => $transactionId,
            'amount' => number_format($payment['amount'], 2),
            'reason' => $payment['failure_reason'] ?? 'Payment was not completed',
            'date' => date('d M, Y h:i A', strtotime($payment['updated_at'])),
            'status' => 'Failed'
        ];

        return view('payment_failure', $data);
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
