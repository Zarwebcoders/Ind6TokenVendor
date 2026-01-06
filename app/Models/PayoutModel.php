<?php

namespace App\Models;

use CodeIgniter\Model;

class PayoutModel extends Model
{
    protected $table = 'payouts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'vendor_id',
        'amount',
        'txn_id',
        'gateway_order_id',
        'gateway_name',
        'gateway_response',
        'beneficiary_name',
        'account_number',
        'ifsc_code',
        'bank_name',
        'utr',
        'status',
        'method',
        'failure_reason',
        'verify_source',
        'created_at',
        'updated_at',
        'completed_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'vendor_id' => 'required|integer',
        'amount' => 'required|decimal',
        'txn_id' => 'required|string|max_length[100]',
        'beneficiary_name' => 'required|string|max_length[255]',
        'account_number' => 'required|string|max_length[50]',
        'ifsc_code' => 'required|string|max_length[20]',
        'status' => 'required|in_list[pending,processing,completed,failed]'
    ];

    protected $validationMessages = [
        'vendor_id' => [
            'required' => 'Vendor ID is required',
            'integer' => 'Vendor ID must be an integer'
        ],
        'amount' => [
            'required' => 'Amount is required',
            'decimal' => 'Amount must be a valid decimal number'
        ],
        'beneficiary_name' => [
            'required' => 'Beneficiary name is required'
        ],
        'account_number' => [
            'required' => 'Account number is required'
        ],
        'ifsc_code' => [
            'required' => 'IFSC code is required'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get payouts by vendor ID
     */
    public function getByVendor($vendorId, $limit = 10, $offset = 0)
    {
        return $this->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);
    }

    /**
     * Get payout by transaction ID
     */
    public function getByTxnId($txnId)
    {
        return $this->where('txn_id', $txnId)->first();
    }

    /**
     * Get payout by gateway order ID
     */
    public function getByGatewayOrderId($gatewayOrderId)
    {
        return $this->where('gateway_order_id', $gatewayOrderId)->first();
    }

    /**
     * Get pending payouts
     */
    public function getPending($limit = 50)
    {
        return $this->where('status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->findAll($limit);
    }

    /**
     * Get processing payouts
     */
    public function getProcessing($limit = 50)
    {
        return $this->where('status', 'processing')
            ->orderBy('created_at', 'ASC')
            ->findAll($limit);
    }

    /**
     * Get completed payouts
     */
    public function getCompleted($vendorId = null, $limit = 10, $offset = 0)
    {
        $builder = $this->where('status', 'completed');

        if ($vendorId) {
            $builder->where('vendor_id', $vendorId);
        }

        return $builder->orderBy('completed_at', 'DESC')
            ->findAll($limit, $offset);
    }

    /**
     * Get failed payouts
     */
    public function getFailed($vendorId = null, $limit = 10, $offset = 0)
    {
        $builder = $this->where('status', 'failed');

        if ($vendorId) {
            $builder->where('vendor_id', $vendorId);
        }

        return $builder->orderBy('updated_at', 'DESC')
            ->findAll($limit, $offset);
    }

    /**
     * Get total payout amount by vendor
     */
    public function getTotalByVendor($vendorId, $status = 'completed')
    {
        $result = $this->selectSum('amount')
            ->where('vendor_id', $vendorId)
            ->where('status', $status)
            ->first();

        return $result['amount'] ?? 0;
    }
}
