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

        // Default values
        $upiId = $vendor['upi_id'] ?? 'abuzarmunshi12-2@okaxis';
        $amount = $payment['amount'];
        $orderRef = $payment['platform_txn_id'];
        $upiString = "upi://pay?pa={$upiId}&pn=" . urlencode($vendor['business_name'] ?? 'Merchant') .
            "&am={$amount}&cu=INR&tn=" . urlencode("Payment for Order {$orderRef}") .
            "&tr={$orderRef}";
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($upiString);

        // Override if it's VMPE or Kay2Pay and we have dynamic data
        if (in_array($payment['gateway_name'], ['vmpe', 'kay2pay'])) {
            // Start by disabling local QR for these gateways
            $qrCodeUrl = null;

            if (!empty($payment['gateway_response'])) {
                $resp = json_decode($payment['gateway_response'], true);

                // Extract URL from response (common fields for both gateways)
                $gatewayUrl = $resp['payment_url'] ?? $resp['data']['payment_url'] ?? $resp['qr_code'] ?? $resp['qr_url'] ??
                    $resp['order_details']['qr_code'] ?? $resp['order_details']['deeplink'] ??
                    $resp['qrString'] ?? null;

                if ($gatewayUrl) {
                    $upiString = $gatewayUrl;

                    // ONLY use as QR if it's an actual image
                    if (strpos($gatewayUrl, 'data:image') === 0 || (strpos($gatewayUrl, 'http') === 0 && (strpos($gatewayUrl, '.png') !== false || strpos($gatewayUrl, 'qr') !== false))) {
                        $qrCodeUrl = $gatewayUrl;
                    }
                }
            }
        }

        // Prepare data for view
        $data = [
            'transaction_id' => $transactionId,
            'amount' => number_format($payment['amount'], 2),
            'order_id' => $payment['platform_txn_id'],
            'upi_id' => $upiId,
            'upi_string' => $upiString,
            'qr_code_url' => $qrCodeUrl,
            'gateway_name' => $payment['gateway_name'] ?? 'gateway',
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
        $transactionId = $this->request->getGet('txn') ?? $this->request->getGet('order_id');

        if (!$transactionId) {
            return redirect()->to('/');
        }

        $payment = $this->paymentModel->where('platform_txn_id', $transactionId)
            ->orWhere('txn_id', $transactionId)
            ->first();

        if (!$payment) {
            return redirect()->to('/')->with('error', 'Payment not found');
        }

        $data = [
            'transaction_id' => $transactionId,
            'amount' => number_format($payment['amount'], 2),
            'utr' => $payment['gateway_txn_id'] ?? $payment['utr'] ?? 'N/A',
            'date' => date('d M, Y h:i A', strtotime($payment['updated_at'])),
            'status' => 'Success',
            'gateway' => $payment['gateway_name'] ?? 'Payment Gateway',
            'payment_method' => $payment['method'] ?? 'N/A'
        ];

        return view('payment_success', $data);
    }

    /**
     * Handle Paytm Success Response
     * This is called after Paytm redirects back with success
     */
    public function paytmSuccess()
    {
        $orderId = $this->request->getGet('order_id') ?? $this->request->getGet('ORDERID');

        log_message('info', 'Paytm Success Page accessed with order_id: ' . ($orderId ?? 'NULL'));

        if (!$orderId) {
            log_message('error', 'No order ID provided in Paytm success callback');
            return redirect()->to('/payment/test')->with('error', 'Invalid payment response - No order ID');
        }

        // Get payment details
        $payment = $this->paymentModel->where('txn_id', $orderId)->first();

        log_message('info', 'Payment lookup result for Paytm: ' . ($payment ? 'Found' : 'Not found'));

        if (!$payment) {
            log_message('error', 'Payment not found for order_id: ' . $orderId);
            return redirect()->to('/payment/test')->with('error', 'Payment not found: ' . $orderId);
        }

        // Check if payment is successful
        if ($payment['status'] === 'success') {
            $data = [
                'transaction_id' => $orderId,
                'amount' => number_format($payment['amount'], 2),
                'utr' => $payment['utr'] ?? $payment['gateway_order_id'] ?? 'N/A',
                'bank_txn_id' => $payment['gateway_txn_id'] ?? 'N/A',
                'date' => date('d M, Y h:i A', strtotime($payment['updated_at'] ?? $payment['created_at'])),
                'status' => 'Success',
                'gateway' => 'Paytm',
                'payment_method' => $payment['method'] ?? 'paytm_gateway'
            ];

            return view('payment_success', $data);
        } elseif ($payment['status'] === 'pending') {
            // Payment is still pending, show processing page
            return redirect()->to('/payment/checkout?txn_id=' . $orderId)
                ->with('info', 'Payment is being processed. Please wait...');
        } else {
            // Payment failed
            return redirect()->to('/payment/failure?txn=' . $orderId);
        }
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
