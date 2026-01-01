<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\BankDetailModel;

class CheckUpi extends Controller {
    public function index() {
        $model = new BankDetailModel();
        $row = $model->find(1);
        echo "UPI ID LENGTH: " . strlen($row['upi_id']) . "\n";
        echo "UPI ID VALUE: [" . $row['upi_id'] . "]\n";
    }
}
