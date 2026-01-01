<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VendorModel;

class VendorAuth extends BaseController
{
    public function login()
    {
         return view('auth/login');
    }

    public function register()
    {
        if (session()->get('isVendorLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/vendor_register');
    }

    public function registerProcess()
    {
        $rules = [
            'name'           => 'required|min_length[3]|max_length[150]',
            'email'          => 'required|valid_email|is_unique[vendors.email]',
            'phone'          => 'required|min_length[10]|max_length[15]|is_unique[vendors.phone]',
            'wallet_address' => 'permit_empty|min_length[10]|max_length[255]',
            'password'       => 'required|min_length[6]',
            'business_name'  => 'permit_empty|max_length[200]',
            'referral_code'  => 'permit_empty|alpha_numeric',
            'profile_image'  => 'permit_empty|uploaded[profile_image]|max_size[profile_image,2048]|is_image[profile_image]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $vendorModel = new VendorModel();

        $data = [
            'name'           => $this->request->getPost('name'),
            'email'          => $this->request->getPost('email'),
            'phone'          => $this->request->getPost('phone'),
            'wallet_address' => $this->request->getPost('wallet_address'),
            'password'       => $this->request->getPost('password'),
            'business_name'  => $this->request->getPost('business_name'),
            'referral_code'  => $this->request->getPost('referral_code'),
            'status'         => 'active', // Default status
            'kyc_status'     => 'pending', // Default KYC
        ];
        
        // Handle file upload
        $image = $this->request->getFile('profile_image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
             $newName = $image->getRandomName();
             $image->move(FCPATH . 'uploads/vendors', $newName);
             $data['profile_image'] = $newName;
        }

        if ($vendorModel->insert($data)) {
            return redirect()->to(base_url('login'))->with('success', 'Registration successful. Please login.');
        } else {
             return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }
    }
}