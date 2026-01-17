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
}
