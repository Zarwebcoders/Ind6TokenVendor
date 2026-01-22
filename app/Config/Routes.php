<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Set default namespace and controller
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Routes start here

$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::loginProcess');
$routes->get('auth/logout', 'Auth::logout');

$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('user-profile', 'Home::userProfile');
$routes->post('user-profile/update', 'Auth::updateProfile', ['filter' => 'auth']);
$routes->get('auth/register', 'Home::authRegister'); // Keep this for now, though admin registration might not be public
$routes->get('utilities/form', 'Home::utilitiesForm', ['filter' => 'auth']);
$routes->get('utilities/vendors', 'Home::utilitiesTable', ['filter' => 'auth']);
$routes->get('utilities/transactions', 'Home::utilitiesTransactions', ['filter' => 'auth']);
$routes->get('utilities/typography', 'Home::utilitiesTypography', ['filter' => 'auth']);
$routes->get('docs/payment-api', 'Home::docsPaymentApi', ['filter' => 'auth']);
$routes->post('bank/save', 'Bank::save', ['filter' => 'auth']);

// Vendor Routes

$routes->get('register', 'VendorAuth::register');
$routes->post('register', 'VendorAuth::registerProcess');
$routes->get('login', 'VendorAuth::login'); // Placeholder

// API Routes for Payment Gateway
$routes->post('api/payment/initiate', 'PaymentApi::initiate');
$routes->post('api/payment/verify', 'PaymentApi::verifyPayment');
$routes->post('api/payment/query', 'PaymentApi::queryStatus');

// Vendor External API (V1)
$routes->group('api/v1', ['filter' => 'vendorAuth'], function ($routes) {
    $routes->post('payment/create', 'VendorApiController::createPayment');
    $routes->post('payment/status', 'VendorApiController::getStatus');
});

// Payraizen Gateway Routes
$routes->post('api/payment/payraizen/initiate', 'PaymentApi::createPayraizenRequest');
$routes->post('api/payment/payraizen/webhook', 'PaymentApi::handlePayraizenWebhook');
$routes->get('api/payment/payraizen/test-webhook', 'PaymentApi::testPayraizenWebhook'); // Test endpoint

// Payraizen Payout Routes
$routes->post('api/payout/payraizen/initiate', 'PaymentApi::createPayraizenPayout');
$routes->post('api/payout/payraizen/webhook', 'PaymentApi::handlePayraizenPayoutWebhook');


// LocalPaisa Gateway Routes
$routes->post('api/payment/localpaisa/initiate', 'PaymentTest::createLocalPaisaRequest');
$routes->post('payment/localpaisa/webhook', 'PaymentTest::handleLocalPaisaWebhook');

// Paytm Gateway Routes (Automatic Verification)
$routes->post('api/paytm/initiate', 'PaytmGatewayApi::initiatePayment');
$routes->post('api/paytm/upi/initiate', 'PaytmGatewayApi::initiateUpiPayment'); // UPI Direct Payment
$routes->post('api/paytm/check-status', 'PaytmGatewayApi::checkStatus');
$routes->post('api/paytm/callback', 'PaytmGatewayApi::callback');
$routes->get('api/paytm/callback', 'PaytmGatewayApi::callback');

// VMPE Gateway Routes (KdsTechs Integration)
$routes->post('api/vmpe/initiate', 'VmpeGatewayApi::initiatePayment');
$routes->post('api/vmpe/webhook', 'VmpeGatewayApi::handleWebhook');
$routes->post('api/vmpe/check-status', 'VmpeGatewayApi::checkStatus');

// Kay2Pay Gateway Routes
$routes->post('api/kay2pay/initiate', 'Kay2PayGatewayApi::initiatePayment');
$routes->post('api/kay2pay/webhook', 'Kay2PayGatewayApi::handleWebhook');
$routes->post('api/kay2pay/check-status', 'Kay2PayGatewayApi::checkStatus');
$routes->post('api/kay2pay/payout/initiate', 'Kay2PayPayoutApi::initiatePayout');
$routes->post('api/kay2pay/payout/check-status', 'Kay2PayPayoutApi::checkStatus');

// BharatPe Gateway Routes (Automatic Verification)
$routes->post('api/bharatpe/create', 'BharatPeApi::createPayment');
$routes->post('api/bharatpe/check-status', 'BharatPeApi::checkStatus');
$routes->post('api/bharatpe/callback', 'BharatPeApi::callback');
$routes->get('api/bharatpe/callback', 'BharatPeApi::callback');

// Payment Checkout Routes
$routes->get('payment/checkout', 'PaymentCheckout::index');
$routes->post('payment/check-status', 'PaymentCheckout::checkStatus');
$routes->get('payment/success', 'PaymentCheckout::success');
$routes->get('payment/paytm/success', 'PaymentCheckout::paytmSuccess'); // Paytm specific success handler
$routes->get('payment/failure', 'PaymentCheckout::failure');
$routes->get('payment/pending', 'PaymentCheckout::pending');

// Webhook Routes
$routes->post('webhook/payment', 'PaymentWebhook::handleWebhook');

// Testing Routes (REMOVE IN PRODUCTION!)
$routes->post('payment/simulate/success', 'PaymentWebhook::simulateSuccess');
$routes->post('payment/simulate/failure', 'PaymentWebhook::simulateFailure');
$routes->get('payment/test', 'PaymentTest::index');
$routes->get('payment/paytm/test', function () {
    return view('paytm_test');
});
$routes->get('payment/vmpe/test', function () {
    return view('vmpe_test');
});
$routes->get('payment/kay2pay/test', function () {
    return view('kay2pay_test');
});
$routes->get('payout/kay2pay/test', function () {
    return view('kay2pay_payout_test');
});

// Vendor Dashboard & Settings Routes (Protected by Auth)
$routes->group('vendor', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'VendorDashboard::index');
    $routes->get('api-settings', 'VendorDashboard::apiSettings');
    $routes->post('api-settings/update', 'VendorDashboard::updateApiSettings');
    $routes->get('kyc', 'VendorDashboard::kyc');
    $routes->post('kyc/update', 'VendorDashboard::updateKyc');
    $routes->get('api-docs', 'VendorDashboard::apiDocs');
});
$routes->post('api/payment/test/create', 'PaymentTest::createTestPayment');




