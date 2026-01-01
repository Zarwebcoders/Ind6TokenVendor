# 💳 Paytm UPI Payment Integration - Complete Setup

## 🎯 Overview

This integration enables **automatic Paytm UPI payment processing** with **real-time status tracking** for your application. Users can pay via UPI QR codes or Paytm gateway, and payments are automatically verified without manual intervention.

---

## ✨ Features

- ✅ **Direct UPI Payments** - Generate UPI intent links for any UPI app (GPay, PhonePe, Paytm, etc.)
- ✅ **QR Code Support** - Dynamic QR code generation for easy scanning
- ✅ **Paytm Gateway** - Full Paytm payment page integration
- ✅ **Automatic Verification** - Real-time payment status tracking via Paytm API
- ✅ **Multiple Payment Methods** - UPI, Cards, Wallets, Net Banking
- ✅ **Secure** - SHA-256 HMAC checksum validation
- ✅ **Database Integration** - Automatic payment record management
- ✅ **Mobile Optimized** - Deep linking to UPI apps

---

## 🚀 Quick Start (5 Minutes)

### Step 1: Your Credentials Are Already Configured!

The system is pre-configured with your test credentials in `app/Config/Paytm.php`:

```php
✅ Merchant ID: hhdaGX54213527799734
✅ Merchant Key: 1OLuORB&@k13LBXv
✅ UPI VPA: abuzarmunshi12-2@okaxis
✅ Business Name: Zarwebcoders
✅ Environment: Staging (Test Mode)
```

### Step 2: Test It Now!

Open in your browser:
```
http://localhost/Ind6TokenVendor/paytm_upi_test.html
```

### Step 3: Make a Test Payment

1. **Enter amount:** `10.00`
2. **Click:** "Pay with UPI" 📱
3. **Scan QR code** with any UPI app
4. **Complete payment** in your UPI app
5. **Watch status auto-update!** ✅

**That's it!** The payment will be automatically verified and updated in your database.

---

## 📱 Payment Methods

### Method 1: UPI Intent (Recommended)

**Best for:** Mobile apps, QR code payments, all UPI apps

**API Endpoint:**
```bash
POST /api/paytm/upi/initiate
```

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
  "upi_intent": "upi://pay?pa=abuzarmunshi12-2@okaxis&...",
  "paytm_intent": "paytmmp://cash_wallet?...",
  "qr_string": "upi://pay?pa=...",
  "payment_info": {
    "amount": 100,
    "payee_vpa": "abuzarmunshi12-2@okaxis",
    "payee_name": "Zarwebcoders"
  }
}
```

**Features:**
- Works with ALL UPI apps
- QR code support
- Deep linking to apps
- Automatic verification

### Method 2: Paytm Gateway

**Best for:** Web payments, multiple payment options

**API Endpoint:**
```bash
POST /api/paytm/initiate
```

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
  "payment_url": "https://securegw-stage.paytm.in/...",
  "params": {
    "MID": "...",
    "ORDER_ID": "...",
    "CHECKSUMHASH": "..."
  }
}
```

**Features:**
- Full Paytm payment page
- Cards, UPI, Wallet, Net Banking
- Automatic callback handling

---

## 🔄 Automatic Status Tracking

### How It Works

```
1. Payment Initiated → Database: "pending"
2. User Pays → In UPI app
3. Auto-Check → Poll API every 3 seconds
4. Status Update → Database: "success"
5. User Notified → Real-time update
```

### Check Status API

**Endpoint:**
```bash
POST /api/paytm/check-status
```

**Request:**
```json
{
  "order_id": "PTM_ABC123"
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

**Response (Pending):**
```json
{
  "status": "pending",
  "message": "Waiting for payment"
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

## 💻 Integration Examples

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
    
    // Or open UPI app directly
    window.location.href = data.upi_intent;
    
    // Start automatic status checking
    checkStatus(data.order_id);
}

// Auto-check payment status every 3 seconds
async function checkStatus(orderId) {
    const interval = setInterval(async () => {
        const response = await fetch('/api/paytm/check-status', {
            method: 'POST',
            body: JSON.stringify({ order_id: orderId })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            clearInterval(interval);
            alert('Payment successful! 🎉');
        } else if (data.status === 'error') {
            clearInterval(interval);
            alert('Payment failed ❌');
        }
    }, 3000);
}
```

### Backend (PHP)

```php
// In your controller
public function createPayment()
{
    $amount = $this->request->getPost('amount');
    
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

## 💾 Database

All payments are automatically saved to the `payments` table:

```sql
-- View all payments
SELECT * FROM payments ORDER BY created_at DESC;

-- View pending payments
SELECT * FROM payments WHERE status = 'pending';

-- View successful payments
SELECT * FROM payments WHERE status = 'success';

-- View specific payment
SELECT * FROM payments WHERE txn_id = 'PTM_ABC123';
```

**Payment Record Example:**
```json
{
  "id": 1,
  "vendor_id": 1,
  "amount": 100.00,
  "txn_id": "PTM_UPI_ABC123",
  "status": "success",
  "method": "paytm_upi",
  "gateway_name": "paytm",
  "utr": "987654321012",
  "gateway_response": "{...}",
  "verify_source": "paytm_api",
  "completed_time": "2023-12-23 18:30:00",
  "created_at": "2023-12-23 18:25:00"
}
```

---

## 🔐 Security

### Checksum Validation

All requests use SHA-256 HMAC:

```php
$checksum = hash_hmac('sha256', $paramString, $merchantKey);
```

### Callback Verification

All callbacks are verified:

```php
if (!$this->verifyChecksum($response, $checksum, $key)) {
    return $this->fail('Invalid checksum');
}
```

---

## 🧪 Testing

### Test Page
```
http://localhost/Ind6TokenVendor/paytm_upi_test.html
```

### Test Scenarios

1. **UPI Payment**
   - Amount: `10.00`
   - Click "Pay with UPI"
   - Scan QR code
   - Complete in UPI app
   - Status auto-updates

2. **Gateway Payment**
   - Amount: `10.00`
   - Click "Pay with Gateway"
   - Redirected to Paytm
   - Complete payment
   - Redirected back

**Note:** No real money charged in test mode!

---

## 🚨 Troubleshooting

### Payment not updating?

**Check:**
1. Is polling running? (Browser console)
2. Credentials correct? (`app/Config/Paytm.php`)
3. Logs: `writable/logs/log-*.php`

**Fix:**
```bash
# Check logs
type writable\logs\log-2023-12-23.php

# Check database
SELECT * FROM payments WHERE status = 'pending';
```

### QR code not working?

**Check:**
1. UPI VPA correct?
2. Try different UPI app
3. Check browser console

### Callback not received?

**Check:**
1. Callback URL accessible?
2. Route configured?
3. Check logs

---

## 📚 Documentation

### Quick References
- **Quick Start:** `docs/PAYTM_QUICKSTART.md`
- **Full Guide:** `docs/PAYTM_UPI_GUIDE.md`
- **Implementation:** `PAYTM_IMPLEMENTATION.md`

### External Links
- [Paytm Developer Docs](https://developer.paytm.com/)
- [UPI Integration Guide](https://developer.paytm.com/docs/upi-integration/)

---

## 📁 Files Structure

```
Ind6TokenVendor/
├── app/
│   ├── Config/
│   │   ├── Paytm.php              ← Configuration
│   │   └── Routes.php             ← API routes
│   └── Controllers/
│       └── PaytmGatewayApi.php    ← Payment controller
├── public/
│   └── paytm_upi_test.html        ← Test page
├── docs/
│   ├── PAYTM_QUICKSTART.md        ← Quick start
│   └── PAYTM_UPI_GUIDE.md         ← Full guide
└── PAYTM_IMPLEMENTATION.md        ← This file
```

---

## 🎓 Next Steps

### For Development
1. ✅ Test with different amounts
2. ✅ Integrate into your app
3. ✅ Customize UI
4. ✅ Add webhooks
5. ✅ Set up monitoring

### For Production
1. Update credentials in `app/Config/Paytm.php`
2. Change environment to `production`
3. Update website to `DEFAULT`
4. Test with real small amounts
5. Enable HTTPS
6. Set up monitoring
7. Remove test routes

---

## ✅ Production Checklist

Before going live:

- [ ] Update to production credentials
- [ ] Change `environment` to `'production'`
- [ ] Change `website` to `'DEFAULT'`
- [ ] Update UPI VPA to production
- [ ] Test with real small amounts
- [ ] Enable HTTPS
- [ ] Set up error monitoring
- [ ] Configure webhook URL
- [ ] Remove test routes
- [ ] Set up alerts

---

## 🆘 Support

**Need Help?**

1. Check documentation in `docs/`
2. Review logs in `writable/logs/`
3. Check Paytm dashboard
4. Verify database `payments` table

**Common Issues:**
- Payment not updating → Check polling
- QR not working → Verify UPI VPA
- Callback not received → Check URL accessibility

---

## 🎉 You're All Set!

Your Paytm UPI payment integration is **complete and ready to use**!

**Start Testing:**
```
http://localhost/Ind6TokenVendor/paytm_upi_test.html
```

**Questions?**
- Read: `docs/PAYTM_QUICKSTART.md`
- Full docs: `docs/PAYTM_UPI_GUIDE.md`

---

**Version:** 1.0.0  
**Last Updated:** December 23, 2023  
**Status:** ✅ Complete and Tested
