<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $vendorModel = new \App\Models\VendorModel();
        $paymentModel = new \App\Models\PaymentModel();

        // Get current vendor ID from session
        $vendorId = session()->get('id');

        // 1. Total tokens sold (Count of successful payments for this vendor)
        $tokensSold = $paymentModel->where('status', 'success')
                                   ->where('vendor_id', $vendorId)
                                   ->countAllResults();

        // 2. Total Transaction (Sum of success payments for this vendor)
        $totalTransaction = $paymentModel->where('status', 'success')
                                         ->where('vendor_id', $vendorId)
                                         ->selectSum('amount')->get()->getRow()->amount ?? 0;

        // 3. Monthly Transaction (Sum of success payments in current month for this vendor)
        $monthlyTransaction = $paymentModel->where('status', 'success')
            ->where('vendor_id', $vendorId)
            ->where('created_at >=', date('Y-m-01 00:00:00'))
            ->selectSum('amount')->get()->getRow()->amount ?? 0;

        // 4. Total Payments (Count of all payments for this vendor) - Replaces Total Vendors
        $totalPayments = $paymentModel->where('vendor_id', $vendorId)->countAllResults();

        // 5. Recent Payments (Last 5 payments for this vendor) - Replaces Top Vendors
        $recentPayments = $paymentModel->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->find();

        // 6. Monthly Sales Data (for Chart)
        $currentYear = date('Y');
        $monthlyEarnings = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = date("$currentYear-$m-01 00:00:00");
            $monthEnd = date("$currentYear-$m-t 23:59:59");
            
            $earning = $paymentModel->where('status', 'success')
                ->where('vendor_id', $vendorId)
                ->where('created_at >=', $monthStart)
                ->where('created_at <=', $monthEnd)
                ->selectSum('amount')->get()->getRow()->amount ?? 0;
            
            $monthlyEarnings[] = (float)$earning;
        }

        // 7. Yearly Breakup Data (Sales for Current Year vs Previous Year)
        $previousYear = $currentYear - 1;
        
        $currentYearSales = $paymentModel->where('status', 'success')
            ->where('vendor_id', $vendorId)
            ->where('created_at >=', date("$currentYear-01-01 00:00:00"))
            ->where('created_at <=', date("$currentYear-12-31 23:59:59"))
            ->selectSum('amount')->get()->getRow()->amount ?? 0;

        $previousYearSales = $paymentModel->where('status', 'success')
            ->where('vendor_id', $vendorId)
            ->where('created_at >=', date("$previousYear-01-01 00:00:00"))
            ->where('created_at <=', date("$previousYear-12-31 23:59:59"))
            ->selectSum('amount')->get()->getRow()->amount ?? 0;
            
        // Calculate Yearly Growth
        $yearlyGrowth = 0;
        if ($previousYearSales > 0) {
            $yearlyGrowth = (($currentYearSales - $previousYearSales) / $previousYearSales) * 100;
        } elseif ($currentYearSales > 0) {
            $yearlyGrowth = 100;
        }

        // 8. Monthly Growth (Current Month vs Same Month Last Year)
        $currentMonth = date('m');
        $lastYearSameMonthStart = date("$previousYear-$currentMonth-01 00:00:00");
        $lastYearSameMonthEnd = date("$previousYear-$currentMonth-t 23:59:59");
        
        $monthlyTransactionLastYear = $paymentModel->where('status', 'success')
            ->where('vendor_id', $vendorId)
            ->where('created_at >=', $lastYearSameMonthStart)
            ->where('created_at <=', $lastYearSameMonthEnd)
            ->selectSum('amount')->get()->getRow()->amount ?? 0;
            
        $monthlyGrowth = 0;
        if ($monthlyTransactionLastYear > 0) {
            $monthlyGrowth = (($monthlyTransaction - $monthlyTransactionLastYear) / $monthlyTransactionLastYear) * 100;
        } elseif ($monthlyTransaction > 0) {
            $monthlyGrowth = 100;
        }

        $data = [
            'tokensSold'         => $tokensSold,
            'totalTransaction'   => $totalTransaction,
            'monthlyTransaction' => $monthlyTransaction,
            'totalPayments'      => $totalPayments, // Changed key
            'recentPayments'     => $recentPayments, // Changed key
            'monthlyEarnings'    => $monthlyEarnings,
            'currentYear'        => $currentYear,
            'previousYear'       => $previousYear,
            'yearlySales'        => [(float)$currentYearSales, (float)$previousYearSales],
            'yearlyGrowth'       => $yearlyGrowth,
            'monthlyGrowth'      => $monthlyGrowth,
        ];

        return view('dashboard', $data);
    }

    public function userProfile(): string
    {
        $adminModel = new \App\Models\AdminModel();
        $userId = session()->get('id');
        $user = $adminModel->find($userId);

        return view('user_profile', ['user' => $user]);
    }

    public function authLogin(): string
    {
        return view('auth/login');
    }

    public function authRegister(): string
    {
        return view('auth/register');
    }

    public function utilitiesForm(): string
    {
        $bankModel = new \App\Models\BankDetailModel();
        // Fetch Admin Bank Details (vendor_id is null)
        $bankDetails = $bankModel->where('vendor_id', null)->first();

        return view('utilities/form', ['bank' => $bankDetails]);
    }

    public function utilitiesTable(): string
    {
        $vendorModel = new \App\Models\VendorModel();
        $vendors = $vendorModel->orderBy('created_at', 'DESC')->findAll();

        return view('utilities/vendors', ['vendors' => $vendors]);
    }

     public function utilitiesTransactions(): string
    {
        $paymentModel = new \App\Models\PaymentModel();
        
        $payments = $paymentModel->select('payments.*, vendors.name as vendor_name, vendors.email as vendor_email')
            ->join('vendors', 'vendors.id = payments.vendor_id', 'left')
            ->orderBy('payments.created_at', 'DESC')
            ->findAll();

        return view('utilities/transactions', ['payments' => $payments]);
    }


    public function utilitiesTypography(): string
    {
        return view('utilities/typography');
    }

    public function docsPaymentApi(): string
    {
        return view('documentation/payment_api');
    }
}
