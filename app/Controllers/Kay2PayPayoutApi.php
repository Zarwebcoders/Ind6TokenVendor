<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\PayoutModel;

class Kay2PayPayoutApi extends Controller
{
    protected $payoutModel;

    // Kay2Pay Payout Configuration
    private $payoutUrl = 'https://kay2pay.in/api/v1/payout/create';
    private $checkUrl = 'https://kay2pay.in/api/v1/payout/check_status';
    private $bearerToken = '0bu9HxJBxIhiHGoEuiwEKuv06W0NcP';

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->payoutModel = new PayoutModel();
    }

    /**
     * Initiate Kay2Pay Payout
     */
    public function initiatePayout()
    {
        try {
            $json = $this->request->getJSON(true);

            log_message('info', 'Kay2Pay Payout Request Received: ' . json_encode($json));

            // Validate mandatory input according to docs
            $requiredFields = ['name', 'account_number', 'ifsc_code', 'bank_name', 'amount', 'email', 'mobile'];
            foreach ($requiredFields as $field) {
                if (empty($json[$field])) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "The {$field} field is required."
                    ]);
                }
            }

            $vendorId = $json['vendor_id'] ?? 1; // Default vendor or get from session
            $amount = $json['amount'];

            // Generate unique merchant ID for udf1
            $merchantTxnId = 'KPY' . time() . rand(1000, 9999);

            // Prepare API request data according to Kay2Pay payout docs
            $requestData = [
                'name' => $json['name'],
                'email' => $json['email'],
                'mobile' => $json['mobile'],
                'account_number' => $json['account_number'],
                'ifsc_code' => $json['ifsc_code'],
                'bank_name' => $json['bank_name'],
                'amount' => $amount,
                'udf1' => $merchantTxnId,
                'udf2' => (string) ($json['udf2'] ?? ''),
                'udf3' => (string) ($json['udf3'] ?? '')
            ];

            log_message('info', 'Kay2Pay Payout API Outgoing Data: ' . json_encode($requestData));

            // Make API request using cURL
            $ch = curl_init($this->payoutUrl);
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

            log_message('info', 'Kay2Pay Payout API Response: ' . $response);

            if ($curlError) {
                log_message('error', 'Kay2Pay Payout cURL Error: ' . $curlError);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Connection error: ' . $curlError
                ]);
            }

            $responseData = json_decode($response, true);
            $apiStatus = strtolower((string) ($responseData['status'] ?? ''));

            if (($httpCode === 200 || $httpCode === 201) && $apiStatus === 'success') {
                $data = $responseData['data'] ?? [];

                // Save payout to database
                $dbData = [
                    'vendor_id' => $vendorId,
                    'amount' => $amount,
                    'txn_id' => $merchantTxnId,
                    'gateway_order_id' => $data['txn_id'] ?? null,
                    'gateway_name' => 'kay2pay',
                    'gateway_response' => $response,
                    'beneficiary_name' => $json['name'],
                    'account_number' => $json['account_number'],
                    'ifsc_code' => $json['ifsc_code'],
                    'bank_name' => $json['bank_name'],
                    'utr' => $data['utr_no'] ?? null,
                    'status' => (strtoupper((string) ($data['status'] ?? '')) === 'SUCCESS') ? 'completed' : 'processing',
                    'method' => 'bank_transfer',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'completed_at' => (strtoupper((string) ($data['status'] ?? '')) === 'SUCCESS') ? date('Y-m-d H:i:s') : null
                ];

                $this->payoutModel->insert($dbData);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $responseData['message'] ?? 'Payout initiated successfully',
                    'txn_id' => $merchantTxnId,
                    'gateway_txn_id' => $data['txn_id'] ?? null,
                    'utr' => $data['utr_no'] ?? null,
                    'status' => $dbData['status']
                ]);
            } else {
                $errorMessage = $responseData['message'] ?? 'Payout initiation failed';
                log_message('error', 'Kay2Pay Payout Failed: ' . $errorMessage);

                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMessage,
                    'raw' => $responseData
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Kay2Pay Payout Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check Kay2Pay Payout Status
     */
    public function checkStatus()
    {
        try {
            $json = $this->request->getJSON(true);
            $txnId = $json['txn_id'] ?? null;

            if (!$txnId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaction ID is required'
                ]);
            }

            // Prepare API request data
            $requestData = [
                'check_by' => 'udf1', // We use udf1 for our merchant ID
                'check_value' => $txnId
            ];

            // Make API request
            $ch = curl_init($this->checkUrl);
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

            log_message('info', "Kay2Pay Payout Status Response for {$txnId}: " . $response);

            $responseData = json_decode($response, true);
            $apiStatus = strtolower((string) ($responseData['status'] ?? ''));

            if ($httpCode === 200 && $apiStatus === 'success' && isset($responseData['data'])) {
                $data = $responseData['data'];
                $txnStatus = strtolower((string) ($data['status'] ?? 'pending'));

                // Update database
                $payout = $this->payoutModel->where('txn_id', $txnId)->first();
                if ($payout) {
                    $updateData = [
                        'utr' => $data['utr_no'] ?? $payout['utr'],
                        'status' => ($txnStatus === 'success') ? 'completed' : (($txnStatus === 'failed') ? 'failed' : 'processing'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    if ($txnStatus === 'success' && empty($payout['completed_at'])) {
                        $updateData['completed_at'] = date('Y-m-d H:i:s');
                    }

                    $this->payoutModel->update($payout['id'], $updateData);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'status' => $txnStatus,
                    'utr' => $data['utr_no'] ?? null,
                    'message' => $responseData['message'] ?? 'Status retrieved'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to retrieve payout status'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Kay2Pay Payout Status Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
