<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'vendor_id', 'amount', 'method', 'txn_id', 'utr', 'reference_no', 
        'status', 'screenshot', 'request_time', 'completed_time', 'verify_source',
        'gateway_name', 'gateway_order_id', 'gateway_response', 'platform_txn_id',
        'buyer_name', 'buyer_email', 'buyer_phone', 'payment_method', 'gateway_txn_id',
        'failure_reason'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
