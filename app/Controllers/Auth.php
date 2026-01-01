<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VendorModel;

class Auth extends BaseController
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url(''));
        }

        return view('auth/login');
    }

    public function loginProcess()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('auth/login')->withInput()->with('errors', $this->validator->getErrors());
        }

        $adminModel = new VendorModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Debug: Log the attempt
        log_message('info', 'Login attempt for email: ' . $email);

        $user = $adminModel->where('email', $email)->first();

        if (!$user) {
            log_message('warning', 'User not found: ' . $email);
            return redirect()->to('auth/login')->withInput()->with('error', 'Invalid email or password.');
        }

        log_message('info', 'User found: ' . $user['email'] . ', comparing passwords');
        log_message('debug', 'Input password: [' . $password . '], Stored password: [' . $user['password'] . ']');

        // Trim passwords to avoid whitespace issues
        $inputPassword = trim($password);
        $storedPassword = trim($user['password']);

        if ($inputPassword === $storedPassword) {
            log_message('info', 'Password matched for user: ' . $user['email']);

            $sessionData = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'isLoggedIn' => true,
            ];
            session()->set($sessionData);

            log_message('info', 'Session set for user: ' . $user['email']);
            log_message('debug', 'Session data: ' . json_encode($sessionData));

            return redirect()->to(base_url(''));
        }

        log_message('warning', 'Password mismatch for user: ' . $email);
        return redirect()->to('auth/login')->withInput()->with('error', 'Invalid email or password.');
    }

    public function updateProfile()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userId = session()->get('id');
        $adminModel = new VendorModel();

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[admins.email,id,{$userId}]",
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        if ($adminModel->update($userId, $data)) {
            // Update session data
            session()->set(['name' => $data['name'], 'email' => $data['email']]);
            return redirect()->back()->with('success', 'Profile updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update profile.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'));
    }
}
