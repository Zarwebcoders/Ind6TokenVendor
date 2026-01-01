<?php

namespace App\Models;

use CodeIgniter\Model;

class BankDetailModel extends Model
{
    protected $table            = 'bank_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'vendor_id', 'account_holder', 'account_number', 'ifsc', 
        'bank_name', 'upi_id', 'upi_qr', 'is_default', 'active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
