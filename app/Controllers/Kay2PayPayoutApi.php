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
    /**
     * Check Kay2Pay Payout Status
     */
    public function checkStatus()
    {
        try {
            $json = $this->request->getJSON(true);

            // Allow flexible input based on API docs: check_by can be txnid/udf1/udf2/udf3
            // Default to 'udf1' (our internal txn_id) if only 'txn_id' is passed in request for backward compatibility
            $checkBy = $json['check_by'] ?? 'udf1';
            $checkValue = $json['check_value'] ?? ($json['txn_id'] ?? null);

            if (!$checkValue) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Check value is required (provide check_value and check_by, or txn_id)'
                ]);
            }

            // Prepare API request data
            $requestData = [
                'check_by' => $checkBy,
                'check_value' => $checkValue
            ];

            log_message('info', 'Kay2Pay Payout Check Status Request: ' . json_encode($requestData));

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
            $curlError = curl_error($ch);
            curl_close($ch);

            log_message('info', "Kay2Pay Payout Status Response for {$checkValue}: " . $response);

            if ($curlError) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Connection error: ' . $curlError
                ]);
            }

            $responseData = json_decode($response, true);
            $apiStatus = strtolower((string) ($responseData['status'] ?? ''));

            // API Level Success
            if ($apiStatus === 'success' && isset($responseData['data'])) {
                $data = $responseData['data'];

                // Transaction Status from 'data.status' (success/failed/pending)
                $txnStatus = strtolower((string) ($data['status'] ?? 'pending'));

                // Map to our DB status
                $dbStatus = 'processing';
                if ($txnStatus === 'success') {
                    $dbStatus = 'completed';
                } elseif ($txnStatus === 'failed') {
                    $dbStatus = 'failed';
                }

                // Find the payout record in DB
                // If check_by was udf1, checkValue is our txn_id.
                // If check_by was txnid, checkValue is gateway_order_id.
                // RELIABLE METHOD: The response 'data' should contain udf1 (our txn_id). Use that if available.

                $payout = null;
                if (!empty($data['udf1'])) {
                    $payout = $this->payoutModel->where('txn_id', $data['udf1'])->first();
                }

                // Fallback: search based on what we sent if response doesn't have udf1
                if (!$payout) {
                    if ($checkBy === 'udf1') {
                        $payout = $this->payoutModel->where('txn_id', $checkValue)->first();
                    } elseif ($checkBy === 'txnid' || $checkBy === 'txn_id') {
                        $payout = $this->payoutModel->where('gateway_order_id', $checkValue)->first();
                    }
                }

                if ($payout) {
                    $updateData = [
                        'status' => $dbStatus,
                        'gateway_response' => $response, // Store latest response logs
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    // Update UTR if present
                    if (!empty($data['utr_no'])) {
                        $updateData['utr'] = $data['utr_no'];
                    }

                    // Update Gateway Transaction ID (from data.txn_id)
                    if (!empty($data['txn_id'])) {
                        $updateData['gateway_order_id'] = $data['txn_id'];
                    }

                    // Handle completion
                    if ($dbStatus === 'completed' && empty($payout['completed_at'])) {
                        $updateData['completed_at'] = date('Y-m-d H:i:s');
                    }

                    // Handle failure reason
                    if ($dbStatus === 'failed') {
                        $updateData['failure_reason'] = $responseData['message'] ?? 'Transaction failed at gateway';
                    }

                    $this->payoutModel->update($payout['id'], $updateData);

                    return $this->response->setJSON([
                        'success' => true,
                        'status' => $txnStatus,
                        'db_status' => $dbStatus,
                        'utr' => $updateData['utr'] ?? null,
                        'message' => $responseData['message'] ?? 'Status updated successfully',
                        'data' => $data
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Payout record not found in database.',
                        'api_data' => $data
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to retrieve payout status',
                'raw' => $responseData
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
