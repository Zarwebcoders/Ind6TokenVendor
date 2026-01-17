<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PaymentModel;
use App\Models\VendorModel;

class VendorApiController extends Controller
{
    protected $paymentModel;
    protected $vendor;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->paymentModel = new PaymentModel();
        // $this->request->vendor is set by the Filter (VendorApiFilter)
        $this->vendor = $this->request->vendor ?? null;
    }

    /**
     * Vendor API: Initiate Payment
     * POST /api/v1/payment/create
     */
    public function createPayment()
    {
        try {
            $json = $this->request->getJSON(true);

            if (empty($json['amount']) || empty($json['customer_name'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required fields: amount and customer_name'
                ])->setStatusCode(400);
            }

            // Here we would normally choose a gateway. 
            // For now, let's use Kay2Pay as the default engine.
            if (!$this->vendor) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized vendor context'
                ])->setStatusCode(401);
            }

            $kay2pay = new Kay2PayGatewayApi();
            // Important: Controllers need to be initialized if called manually
            $kay2pay->initController($this->request, $this->response, $this->logger);

            // Mock the request data that Kay2PayGatewayApi expects
            $internalRequest = [
                'user' => $json['customer_id'] ?? 'external',
                'amount' => $json['amount'],
                'name' => $json['customer_name'],
                'email' => $json['customer_email'] ?? 'customer@example.com',
                'mobile' => $json['customer_mobile'] ?? '9999999999'
            ];

            // Setup the internal request state
            $this->request->setBody(json_encode($internalRequest));

            // Call the initiation logic
            $gatewayResponse = $kay2pay->initiatePayment();

            // Get the JSON result from the response object
            $responseBody = $gatewayResponse->getBody();
            $result = json_decode($responseBody, true);

            if ($result['success']) {
                // Update the payment record with the vendor who initiated it
                $db = \Config\Database::connect();
                $db->table('payment')
                    ->where('txn_id', $result['order_id'])
                    ->update(['vendor_id' => $this->vendor->id]);

                return $this->response->setJSON([
                    'success' => true,
                    'order_id' => $result['order_id'],
                    'payment_url' => $result['payment_url'],
                    'message' => 'Transaction created successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gateway Error'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Vendor API Create Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Internal Server Error'
            ])->setStatusCode(500);
        }
    }

    /**
     * Vendor API: Check Payment Status
     * POST /api/v1/payment/status
     */
    public function getStatus()
    {
        try {
            $json = $this->request->getJSON(true);
            $orderId = $json['order_id'] ?? null;

            if (!$orderId) {
                return $this->response->setJSON(['success' => false, 'message' => 'order_id is required'])->setStatusCode(400);
            }

            // Verify the order belongs to this vendor
            $payment = $this->paymentModel->where('txn_id', $orderId)
                ->where('vendor_id', $this->vendor->id)
                ->first();

            if (!$payment) {
                return $this->response->setJSON(['success' => false, 'message' => 'Transaction not found'])->setStatusCode(404);
            }

            // Use the gateway check mechanism
            $kay2pay = new Kay2PayGatewayApi();
            $kay2pay->initController($this->request, $this->response, $this->logger);

            $this->request->setBody(json_encode(['order_id' => $orderId]));
            $statusResponse = $kay2pay->checkStatus();

            return $this->response->setJSON(json_decode($statusResponse->getBody(), true));

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error checking status'])->setStatusCode(500);
        }
    }
}
