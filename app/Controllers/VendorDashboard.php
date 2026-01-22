<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VendorModel;
use App\Models\PaymentModel;

class VendorDashboard extends BaseController
{
    protected $vendorModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->vendorModel = new VendorModel();
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Vendor Dashboard Home
     */
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $vendorId = session()->get('id');
        $data['vendor'] = $this->vendorModel->find($vendorId);

        // Stats
        $data['total_payments'] = $this->paymentModel->where('vendor_id', $vendorId)->countAllResults();
        $data['success_payments'] = $this->paymentModel->where('vendor_id', $vendorId)->where('status', 'success')->countAllResults();
        $data['recent_payments'] = $this->paymentModel->where('vendor_id', $vendorId)->orderBy('created_at', 'DESC')->limit(10)->findAll();

        return view('vendor/dashboard', $data);
    }

    /**
     * API Settings Page
     */
    public function apiSettings()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $vendorId = session()->get('id');
        $data['vendor'] = $this->vendorModel->find($vendorId);

        return view('vendor/api_settings', $data);
    }

    /**
     * Handle API Settings Update
     */
    public function updateApiSettings()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $vendorId = session()->get('id');

        $action = $this->request->getPost('action');

        if ($action === 'generate_token') {
            $newToken = 'bin_' . bin2hex(random_bytes(16));
            $newSecret = bin2hex(random_bytes(32));

            $this->vendorModel->update($vendorId, [
                'api_token' => $newToken,
                'api_secret' => $newSecret
            ]);

            return redirect()->back()->with('success', 'New API Token generated successfully!');
        }

        if ($action === 'update_ips') {
            $ips = $this->request->getPost('whitelisted_ips');
            $webhook = $this->request->getPost('webhook_url');

            $this->vendorModel->update($vendorId, [
                'whitelisted_ips' => $ips,
                'webhook_url' => $webhook
            ]);

            return redirect()->back()->with('success', 'API Settings updated successfully!');
        }

        return redirect()->back();
    }

    /**
     * Developer Documentation
     */
    public function apiDocs()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $vendorId = session()->get('id');
        $data['vendor'] = $this->vendorModel->find($vendorId);

        return view('vendor/api_docs', $data);
    }

    /**
     * KYC Settings Page
     */
    public function kyc()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $vendorId = session()->get('id');
        $data['vendor'] = $this->vendorModel->find($vendorId);

        return view('vendor/kyc', $data);
    }

    /**
     * Handle KYC Update
     */
    public function updateKyc()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $vendorId = session()->get('id');
        $vendor = $this->vendorModel->find($vendorId);

        $rules = [
            'pan_number' => 'required',
            'gst_number' => 'permit_empty',
            'hsn_code' => 'permit_empty',
            'address' => 'required',
            'city' => 'required',
            'pincode' => 'required|numeric|min_length[6]|max_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'pan_number' => $this->request->getPost('pan_number'),
            'gst_number' => $this->request->getPost('gst_number'),
            'hsn_code' => $this->request->getPost('hsn_code'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'pincode' => $this->request->getPost('pincode'),
            'kyc_status' => 'pending' // Reset to pending when updated
        ];

        // Handle File Uploads
        $panDoc = $this->request->getFile('pan_document');
        if ($panDoc && $panDoc->isValid() && !$panDoc->hasMoved()) {
            $newName = $panDoc->getRandomName();
            $panDoc->move(FCPATH . 'uploads/kyc/' . $vendorId, $newName);
            $updateData['pan_document'] = 'uploads/kyc/' . $vendorId . '/' . $newName;
        }

        $gstDoc = $this->request->getFile('gst_document');
        if ($gstDoc && $gstDoc->isValid() && !$gstDoc->hasMoved()) {
            $newName = $gstDoc->getRandomName();
            $gstDoc->move(FCPATH . 'uploads/kyc/' . $vendorId, $newName);
            $updateData['gst_document'] = 'uploads/kyc/' . $vendorId . '/' . $newName;
        }

        $this->vendorModel->update($vendorId, $updateData);

        return redirect()->back()->with('success', 'KYC details submitted successfully! Our team will review it soon.');
    }
}
