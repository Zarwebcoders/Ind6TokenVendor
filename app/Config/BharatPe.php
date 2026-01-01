<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class BharatPe extends BaseConfig
{
    /**
     * BharatPe Merchant Configuration
     * Get these credentials from your BharatPe merchant dashboard
     */
    
    // Your BharatPe Merchant ID (from dashboard)
    public $merchantId = '919241120006';
    
    // API Key (from BharatPe dashboard - API section)
    public $apiKey = 'YOUR_BHARATPE_API_KEY';
    
    // API Secret (from BharatPe dashboard - API section)
    public $apiSecret = 'YOUR_BHARATPE_API_SECRET';
    
    // BharatPe API Base URL
    // Production: https://api.bharatpe.in/v1
    // Sandbox: https://sandbox-api.bharatpe.in/v1
    public $baseUrl = 'https://api.bharatpe.in/v1';
    
    // Webhook callback URL (will be auto-generated)
    public $callbackUrl = '';
    
    // Enable/Disable test mode
    public $testMode = true;
    
    /**
     * Constructor - Auto-generate callback URL
     */
    public function __construct()
    {
        parent::__construct();
        $this->callbackUrl = base_url('api/bharatpe/callback');
    }
}
