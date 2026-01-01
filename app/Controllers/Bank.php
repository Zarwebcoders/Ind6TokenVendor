<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BankDetailModel;

class Bank extends BaseController
{
    public function save()
    {
        $bankModel = new BankDetailModel();

        // Validation could be added here
        $rules = [
            'account_holder' => 'required|min_length[3]',
            'account_number' => 'required',
            'ifsc'           => 'required',
            'bank_name'      => 'required',
        ];

        if (!$this->validate($rules)) {
             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id'             => $this->request->getPost('id'), // Add ID for update
            'account_holder' => $this->request->getPost('account_holder'),
            'account_number' => $this->request->getPost('account_number'),
            'ifsc'           => $this->request->getPost('ifsc'),
            'bank_name'      => $this->request->getPost('bank_name'),
            'upi_id'         => $this->request->getPost('upi_id'),
            // vendor_id is null for admin as per requirements "Null = Admin bank"
            // If this was a logged in vendor, we would set session('user_id') here
            'vendor_id'      => null, 
            'is_default'     => 1, // Assuming admin bank details are default for now
            'active'         => 1
        ];

        // Handle UPI QR upload if present
        $qrFile = $this->request->getFile('upi_qr');
        if ($qrFile && $qrFile->isValid() && !$qrFile->hasMoved()) {
            $newName = $qrFile->getRandomName();
            $qrFile->move(FCPATH . 'uploads/qr_codes', $newName);
            $data['upi_qr'] = 'uploads/qr_codes/' . $newName;
        }

        if ($bankModel->save($data)) {
            return redirect()->to(base_url('utilities/form'))->with('success', 'Bank details saved successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to save bank details.');
        }
    }
}
