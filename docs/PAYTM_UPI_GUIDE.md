# Paytm UPI Payment Integration Guide

## Overview

This guide explains how to integrate and use Paytm UPI payments with automatic status tracking in your application.

## Features

✅ **Direct UPI Payments** - Generate UPI intent links for any UPI app  
✅ **Paytm Gateway Integration** - Full Paytm payment gateway support  
✅ **Automatic Status Tracking** - Real-time payment verification via Paytm API  
✅ **QR Code Generation** - Dynamic QR codes for easy payment  
✅ **Multiple Payment Methods** - UPI intent, Paytm app, and web gateway  
✅ **Secure Checksum Validation** - Paytm's official checksum algorithm  

---

## Configuration

### 1. Update Paytm Credentials

Edit `app/Config/Paytm.php`:

```php
public $merchantId = 'YOUR_MERCHANT_ID';
public $merchantKey = 'YOUR_MERCHANT_KEY';
public $website = 'WEBSTAGING'; // or 'DEFAULT' for production
public $upiVpa = 'your-upi-id@paytm'; // Your UPI ID
public $payeeName = 'Your Business Name';
```

### 2. Environment Setup

For **Testing** (Staging):
```php
public $environment = 'staging';
public $website = 'WEBSTAGING';
```

For **Production** (Live):
```php
public $environment = 'production';
public $website = 'DEFAULT';
```

---

## API Endpoints

### 1. Initiate UPI Payment

**Endpoint:** `POST /api/paytm/upi/initiate`

**Request:**
```json
{
  "vendor_id": 1,
  "amount": 100.00
}
```

**Response:**
```json
{
  "success": true,
  "order_id": "PTM_UPI_ABC123",
  "payment_type": "upi_intent",
  "upi_intent": "upi://pay?pa=...",
  "paytm_intent": "paytmmp://cash_wallet?...",
  "qr_string": "upi://pay?pa=...",
  "payment_info": {
    "amount": 100,
    "currency": "INR",
    "payee_vpa": "your-upi@paytm",
    "payee_name": "Your Business"
  },
  "instructions": {
    "message": "Scan QR code or click UPI link to pay",
    "note": "Payment will be automatically verified"
  }
}
```

### 2. Initiate Gateway Payment

**Endpoint:** `POST /api/paytm/initiate`

**Request:**
```json
{
  "vendor_id": 1,
  "amount": 100.00
}
```

**Response:**
```json
{
  "success": true,
  "order_id": "PTM_ABC123",
  "payment_url": "https://securegw-stage.paytm.in/theia/processTransaction",
  "params": {
    "MID": "...",
    "ORDER_ID": "...",
    "TXN_AMOUNT": "100.00",
    "CHECKSUMHASH": "..."
  },
  "amount": 100
}
```

### 3. Check Payment Status

**Endpoint:** `POST /api/paytm/check-status`

**Request:**
```json
{
  "order_id": "PTM_ABC123"
}
```

**Response (Pending):**
```json
{
  "status": "pending",
  "message": "Waiting for payment"
}
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Payment successful",
  "txn_id": "20231223123456789"
}
```

**Response (Failed):**
```json
{
  "status": "error",
  "error": "Payment failed"
}
```

---

## Payment Flow

### UPI Intent Flow

1. **User initiates payment** → Call `/api/paytm/upi/initiate`
2. **Display QR code** → Generate QR from `qr_string`
3. **User scans/clicks** → Opens UPI app with pre-filled details
4. **User completes payment** → In their UPI app
5. **Auto-verification** → Poll `/api/paytm/check-status` every 3-5 seconds
6. **Status updated** → Payment marked as success/failed in database

### Gateway Flow

1. **User initiates payment** → Call `/api/paytm/initiate`
2. **Redirect to Paytm** → Submit form with payment params
3. **User completes payment** → On Paytm's website
4. **Callback received** → Paytm redirects to `/api/paytm/callback`
5. **Status updated** → Payment verified and updated
6. **User redirected** → To success/failure page

---

## Testing

### 1. Open Test Page

Navigate to: `http://localhost/Ind6TokenVendor/paytm_upi_test.html`

### 2. Test UPI Payment

1. Enter amount (e.g., 10.00)
2. Click **"Pay with UPI"**
3. Scan QR code or click **"Open UPI App"**
4. Complete payment in your UPI app
5. Status will auto-update on the page

### 3. Test Gateway Payment

1. Enter amount (e.g., 10.00)
2. Click **"Pay with Gateway"**
3. You'll be redirected to Paytm's payment page
4. Complete payment using test credentials
5. You'll be redirected back with status

### Paytm Test Credentials

For **Staging Environment**:
- **Test Cards:** Use Paytm's test card numbers
- **Test UPI:** Use test UPI IDs provided by Paytm
- **Test Wallet:** Use Paytm test wallet

**Note:** Actual money is NOT deducted in staging mode.

---

## Status Tracking

### Automatic Polling

The system automatically checks payment status every 3-5 seconds:

```javascript
setInterval(async () => {
    const response = await fetch('/api/paytm/check-status', {
        method: 'POST',
        body: JSON.stringify({ order_id: orderId })
    });
    const data = await response.json();
    
    if (data.status === 'success') {
        // Payment successful
        clearInterval(checkInterval);
    }
}, 3000);
```

### Manual Status Check

You can also manually check status:

```bash
curl -X POST http://localhost/Ind6TokenVendor/api/paytm/check-status \
  -H "Content-Type: application/json" \
  -d '{"order_id": "PTM_ABC123"}'
```

---

## Database Schema

Payments are stored in the `payments` table:

```sql
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    txn_id VARCHAR(100) UNIQUE,
    status ENUM('pending', 'success', 'failed'),
    method VARCHAR(50),
    gateway_name VARCHAR(50),
    gateway_order_id VARCHAR(100),
    utr VARCHAR(100),
    gateway_response TEXT,
    verify_source VARCHAR(50),
    completed_time DATETIME,
    created_at DATETIME,
    updated_at DATETIME
);
```

**Key Fields:**
- `txn_id` - Your internal transaction ID
- `gateway_order_id` - Paytm's transaction ID
- `utr` - Bank UTR/Reference number
- `status` - Payment status (pending/success/failed)
- `method` - Payment method (paytm_upi/paytm_gateway)
- `verify_source` - How payment was verified (paytm_api/paytm_callback)

---

## Security

### Checksum Validation

All Paytm requests use SHA-256 HMAC checksum:

```php
private function generateChecksum($params, $key)
{
    ksort($params);
    $paramStr = '';
    foreach ($params as $k => $v) {
        if ($k !== 'CHECKSUMHASH') {
            $paramStr .= $k . '=' . $v . '&';
        }
    }
    $paramStr = rtrim($paramStr, '&');
    
    return hash_hmac('sha256', $paramStr, $key);
}
```

### Callback Verification

All callbacks from Paytm are verified:

```php
$isValidChecksum = $this->verifyChecksum(
    $paytmResponse, 
    $checksumHash, 
    $this->paytmConfig->merchantKey
);

if (!$isValidChecksum) {
    return $this->fail('Invalid checksum');
}
```

---

## Troubleshooting

### Issue: Payment not updating

**Solution:**
1. Check if status polling is running
2. Verify Paytm credentials are correct
3. Check logs: `writable/logs/log-*.php`
4. Ensure callback URL is accessible

### Issue: Invalid checksum error

**Solution:**
1. Verify merchant key is correct
2. Check parameter order (must be sorted)
3. Ensure no extra spaces in parameters

### Issue: QR code not working

**Solution:**
1. Verify UPI VPA is correct
2. Check QR code data length (max ~1000 chars)
3. Test with different UPI apps

### Issue: Callback not received

**Solution:**
1. Ensure callback URL is publicly accessible
2. Check if CORS is enabled
3. Verify route is configured correctly

---

## Production Checklist

Before going live:

- [ ] Update `environment` to `'production'`
- [ ] Update `website` to `'DEFAULT'`
- [ ] Replace test credentials with production credentials
- [ ] Update UPI VPA to production UPI ID
- [ ] Test with small real transactions
- [ ] Set up proper error logging
- [ ] Configure webhook URL
- [ ] Enable HTTPS
- [ ] Remove test routes
- [ ] Set up monitoring and alerts

---

## Support

### Paytm Documentation
- [Paytm Developer Docs](https://developer.paytm.com/)
- [UPI Integration Guide](https://developer.paytm.com/docs/upi-integration/)
- [API Reference](https://developer.paytm.com/docs/api/)

### Contact
For issues with this integration, check:
1. Application logs: `writable/logs/`
2. Paytm dashboard for transaction details
3. Database `payments` table for status

---

## Example Integration

### Frontend (JavaScript)

```javascript
// Initiate UPI Payment
async function payWithUPI(amount) {
    const response = await fetch('/api/paytm/upi/initiate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            vendor_id: 1,
            amount: amount
        })
    });
    
    const data = await response.json();
    
    // Generate QR Code
    QRCode.toCanvas(canvas, data.qr_string);
    
    // Or open UPI app
    window.location.href = data.upi_intent;
    
    // Start polling
    checkStatus(data.order_id);
}

// Check Status
async function checkStatus(orderId) {
    const interval = setInterval(async () => {
        const response = await fetch('/api/paytm/check-status', {
            method: 'POST',
            body: JSON.stringify({ order_id: orderId })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            clearInterval(interval);
            alert('Payment successful!');
        }
    }, 3000);
}
```

### Backend (PHP)

```php
// In your controller
public function processPayment()
{
    $amount = $this->request->getPost('amount');
    
    // Call Paytm API
    $client = \Config\Services::curlrequest();
    $response = $client->post(base_url('api/paytm/upi/initiate'), [
        'json' => [
            'vendor_id' => session()->get('id'),
            'amount' => $amount
        ]
    ]);
    
    $data = json_decode($response->getBody(), true);
    
    return $this->respond($data);
}
```

---

## Changelog

### Version 1.0.0 (2023-12-23)
- ✅ Initial release
- ✅ UPI intent payment support
- ✅ Paytm gateway integration
- ✅ Automatic status tracking
- ✅ QR code generation
- ✅ Checksum validation
- ✅ Callback handling

---

**Last Updated:** December 23, 2023  
**Version:** 1.0.0
