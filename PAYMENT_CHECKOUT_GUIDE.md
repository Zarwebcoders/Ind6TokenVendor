# Payment Checkout System - Complete Guide

This guide explains how to use the new payment checkout system with QR code display, automatic payment capture, and UPI app integration.

## 🎯 Features

1. **QR Code Display** - Shows scannable QR code for UPI payments
2. **Auto Payment Detection** - Automatically checks payment status every 3 seconds
3. **Mobile UPI App Integration** - Opens UPI apps directly on mobile devices
4. **Timer Countdown** - 5-minute payment window with automatic timeout
5. **Success/Failure Screens** - Beautiful animated screens with auto-redirect
6. **Responsive Design** - Works perfectly on desktop and mobile

## 📋 How It Works

### Flow Diagram
```
User Initiates Payment
    ↓
Redirect to Checkout Page
    ↓
Display QR Code + UPI Details
    ↓
User Scans QR / Opens UPI App
    ↓
Auto Status Checking (Every 3s)
    ↓
Payment Success/Failure
    ↓
Show Result Screen
    ↓
Auto Redirect (After 3s)
```

## 🚀 Quick Start

### Step 1: Create a Test Payment

```php
// In your payment initiation code
$paymentData = [
    'vendor_id' => 1,
    'amount' => 100.00,
    'platform_txn_id' => 'TXN_' . time(),
    'status' => 'pending',
    'buyer_name' => 'Test User',
    'buyer_email' => 'test@example.com',
    'buyer_phone' => '9876543210',
    'created_at' => date('Y-m-d H:i:s')
];

$paymentModel = new \App\Models\PaymentModel();
$paymentModel->insert($paymentData);

// Redirect to checkout
return redirect()->to('/payment/checkout?txn_id=' . $paymentData['platform_txn_id']);
```

### Step 2: User Sees Checkout Page

The checkout page (`/payment/checkout?txn_id=TXN_xxx`) displays:
- Amount to pay
- QR code (scannable)
- UPI ID
- Timer (5:00 countdown)
- Payment instructions
- Order details

### Step 3: User Makes Payment

**On Desktop:**
1. User scans QR code with mobile UPI app
2. Confirms payment in app
3. System auto-detects payment

**On Mobile:**
1. User clicks "Pay with UPI" button
2. UPI app opens automatically
3. User confirms payment
4. Returns to browser
5. System auto-detects payment

### Step 4: Auto Status Checking

The page automatically checks payment status every 3 seconds:

```javascript
// Runs automatically in background
function checkPaymentStatus() {
    $.ajax({
        url: '/payment/check-status',
        data: { transaction_id: 'TXN_xxx' },
        success: function(response) {
            if (response.status === 'success') {
                // Show success screen
            } else if (response.status === 'failed') {
                // Show failure screen
            }
            // Otherwise, continue checking
        }
    });
}
```

### Step 5: Result Display

**Success:**
- Green checkmark animation
- Celebration effects (🎁🎉✨)
- Transaction details
- Auto-redirect to success page (3 seconds)

**Failure:**
- Red X animation
- Error message
- Auto-redirect to failure page (3 seconds)

## 🧪 Testing the System

### Method 1: Using Simulation Endpoints

Add these routes to `app/Config/Routes.php`:

```php
// Testing routes (remove in production)
$routes->post('payment/simulate/success', 'PaymentWebhook::simulateSuccess');
$routes->post('payment/simulate/failure', 'PaymentWebhook::simulateFailure');
```

**Test Success:**
```bash
curl -X POST http://localhost/payment/simulate/success \
  -d "transaction_id=TXN_xxx"
```

**Test Failure:**
```bash
curl -X POST http://localhost/payment/simulate/failure \
  -d "transaction_id=TXN_xxx"
```

### Method 2: Using Browser Console

Open checkout page, then in browser console:

**Simulate Success:**
```javascript
$.post('/payment/simulate/success', {
    transaction_id: '<?= $transaction_id ?>'
});
```

**Simulate Failure:**
```javascript
$.post('/payment/simulate/failure', {
    transaction_id: '<?= $transaction_id ?>'
});
```

### Method 3: Manual Database Update

```sql
-- Mark as success
UPDATE payments 
SET status = 'success', 
    gateway_txn_id = 'UTR123456789',
    completed_at = NOW(),
    updated_at = NOW()
WHERE platform_txn_id = 'TXN_xxx';

-- Mark as failed
UPDATE payments 
SET status = 'failed', 
    failure_reason = 'Payment declined',
    updated_at = NOW()
WHERE platform_txn_id = 'TXN_xxx';
```

## 📱 Mobile UPI App Integration

### How UPI Deep Links Work

The system generates UPI payment strings:

```
upi://pay?pa=merchant@upi
  &pn=Merchant%20Name
  &am=100.00
  &cu=INR
  &tn=Payment%20for%20Order%20TXN_xxx
  &tr=TXN_xxx
```

**Parameters:**
- `pa` - Payee UPI ID
- `pn` - Payee Name
- `am` - Amount
- `cu` - Currency (INR)
- `tn` - Transaction Note
- `tr` - Transaction Reference

### Supported UPI Apps

When user clicks "Pay with UPI" on mobile, the system can open:
- Google Pay
- PhonePe
- Paytm
- BHIM
- Any UPI-enabled app

## 🔧 Configuration

### Update Vendor UPI Details

```sql
UPDATE vendors 
SET upi_id = 'yourmerchant@upi',
    business_name = 'Your Business Name'
WHERE id = 1;
```

### Customize Timer Duration

In `payment_checkout.php`, change:

```javascript
let timeLeft = 5 * 60; // 5 minutes
// Change to:
let timeLeft = 10 * 60; // 10 minutes
```

### Customize Status Check Interval

```javascript
// Check every 3 seconds (default)
const statusCheckInterval = setInterval(checkPaymentStatus, 3000);

// Change to 5 seconds:
const statusCheckInterval = setInterval(checkPaymentStatus, 5000);
```

## 🎨 Customization

### Change Colors

In `payment_checkout.php` CSS:

```css
/* Primary gradient */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Change to your brand colors */
background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
```

### Add Logo

```html
<div class="header">
    <img src="<?= base_url('assets/logo.png') ?>" alt="Logo" style="height: 50px;">
    <h1>Complete Payment</h1>
</div>
```

## 📊 Status Flow

```
pending → initiated → processing → success
                                 ↘ failed
                                 ↘ expired
```

**Status Mapping:**
- `pending`, `initiated`, `processing` → Continue checking
- `success`, `completed` → Show success screen
- `failed`, `declined`, `expired` → Show failure screen

## 🔐 Security Considerations

### 1. Webhook Validation

Add signature verification in `PaymentWebhook.php`:

```php
public function handleWebhook()
{
    $signature = $this->request->getHeaderLine('X-Webhook-Signature');
    $rawData = file_get_contents('php://input');
    
    // Verify signature
    $expectedSignature = hash_hmac('sha256', $rawData, 'your-secret-key');
    
    if ($signature !== $expectedSignature) {
        return $this->response->setStatusCode(401);
    }
    
    // Process webhook...
}
```

### 2. CSRF Protection

The status check endpoint should validate CSRF tokens:

```php
// In PaymentCheckout::checkStatus()
if (!$this->validate(['csrf_token' => 'required'])) {
    return $this->response->setJSON(['status' => 'error']);
}
```

### 3. Rate Limiting

Prevent abuse of status check endpoint:

```php
// Add rate limiting
$ip = $this->request->getIPAddress();
$cacheKey = "rate_limit_{$ip}";
$requests = cache($cacheKey) ?? 0;

if ($requests > 100) { // Max 100 requests per minute
    return $this->response->setStatusCode(429);
}

cache()->save($cacheKey, $requests + 1, 60);
```

## 🐛 Troubleshooting

### QR Code Not Showing

**Problem:** QR code image not loading

**Solution:** Check if Google Charts API is accessible:
```javascript
// Alternative: Use a QR code library
// Install: composer require endroid/qr-code
```

### Status Not Updating

**Problem:** Payment status not changing

**Solutions:**
1. Check browser console for AJAX errors
2. Verify database connection
3. Check if transaction ID is correct
4. Test with simulation endpoints

### UPI App Not Opening

**Problem:** "Pay with UPI" button doesn't work

**Solutions:**
1. Ensure you're on a mobile device
2. Check if UPI apps are installed
3. Verify UPI string format
4. Test with different browsers

### Timer Not Working

**Problem:** Countdown not displaying

**Solution:** Check JavaScript console for errors:
```javascript
console.log('Timer started:', timeLeft);
```

## 📞 Support

For issues or questions:
1. Check browser console for errors
2. Check server logs: `writable/logs/log-*.php`
3. Test with simulation endpoints
4. Verify database schema

## 🎯 Next Steps

1. **Integrate with Real Gateway:** Connect to PayRaizen or other payment gateway
2. **Add Notifications:** Send SMS/Email on payment success
3. **Add Analytics:** Track conversion rates
4. **Add Retry Logic:** Allow users to retry failed payments
5. **Add Payment History:** Show user's past transactions

## 📝 Example Integration

```php
// In your payment form submission
public function initiatePayment()
{
    $amount = $this->request->getPost('amount');
    $vendorId = $this->request->getPost('vendor_id');
    
    // Create payment record
    $paymentData = [
        'vendor_id' => $vendorId,
        'amount' => $amount,
        'platform_txn_id' => 'TXN_' . uniqid(),
        'status' => 'pending',
        'buyer_name' => $this->request->getPost('buyer_name'),
        'buyer_email' => $this->request->getPost('buyer_email'),
        'buyer_phone' => $this->request->getPost('buyer_phone'),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $paymentModel = new \App\Models\PaymentModel();
    $paymentModel->insert($paymentData);
    
    // Redirect to checkout
    return redirect()->to('/payment/checkout?txn_id=' . $paymentData['platform_txn_id']);
}
```

---

**That's it!** Your payment checkout system is now ready to use. Test it thoroughly before going live! 🚀
