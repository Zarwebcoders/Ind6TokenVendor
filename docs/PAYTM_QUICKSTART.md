# Paytm UPI Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Configure Credentials

Edit `app/Config/Paytm.php`:

```php
public $merchantId = 'hhdaGX54213527799734'; // Your Merchant ID
public $merchantKey = '1OLuORB&@k13LBXv';    // Your Merchant Key
public $upiVpa = 'abuzarmunshi12-2@okaxis'; // Your UPI ID
public $payeeName = 'Zarwebcoders';          // Your Business Name
```

### Step 2: Test the Integration

Open in browser:
```
http://localhost/Ind6TokenVendor/paytm_upi_test.html
```

### Step 3: Make a Test Payment

1. Enter amount: `10.00`
2. Click **"Pay with UPI"**
3. Scan QR code with any UPI app
4. Complete payment
5. Watch status auto-update! ✅

---

## 📱 Payment Methods

### Method 1: UPI Intent (Recommended)

**Best for:** Mobile apps, QR code payments

```javascript
// API Call
POST /api/paytm/upi/initiate
{
  "vendor_id": 1,
  "amount": 100
}

// Response
{
  "upi_intent": "upi://pay?pa=...",
  "paytm_intent": "paytmmp://...",
  "qr_string": "upi://pay?..."
}
```

**Features:**
- ✅ Works with ALL UPI apps (GPay, PhonePe, Paytm, etc.)
- ✅ QR code support
- ✅ Deep linking to apps
- ✅ Automatic verification

### Method 2: Paytm Gateway

**Best for:** Web payments, card payments

```javascript
// API Call
POST /api/paytm/initiate
{
  "vendor_id": 1,
  "amount": 100
}

// Response
{
  "payment_url": "https://securegw-stage.paytm.in/...",
  "params": { ... }
}
```

**Features:**
- ✅ Full Paytm payment page
- ✅ Multiple payment options
- ✅ Card, UPI, wallet, net banking
- ✅ Automatic callback handling

---

## 🔄 Automatic Status Tracking

### How It Works

1. **Payment Initiated** → Record created with status `pending`
2. **User Pays** → In UPI app or Paytm gateway
3. **Auto-Check** → System polls Paytm API every 3 seconds
4. **Status Updated** → Database updated to `success` or `failed`
5. **Notification** → User sees real-time status

### Implementation

```javascript
// Start polling after payment initiation
setInterval(async () => {
    const response = await fetch('/api/paytm/check-status', {
        method: 'POST',
        body: JSON.stringify({ order_id: 'PTM_ABC123' })
    });
    
    const data = await response.json();
    
    if (data.status === 'success') {
        // Payment successful!
        clearInterval(interval);
    }
}, 3000);
```

---

## 🎯 API Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/paytm/upi/initiate` | POST | Start UPI payment |
| `/api/paytm/initiate` | POST | Start gateway payment |
| `/api/paytm/check-status` | POST | Check payment status |
| `/api/paytm/callback` | POST/GET | Receive payment callback |

---

## 💾 Database

Payments are automatically saved to `payments` table:

```sql
SELECT * FROM payments WHERE txn_id = 'PTM_ABC123';
```

**Key Fields:**
- `status` → `pending`, `success`, `failed`
- `method` → `paytm_upi`, `paytm_gateway`
- `utr` → Bank reference number
- `gateway_response` → Full Paytm response (JSON)

---

## 🧪 Testing

### Test Credentials (Staging)

Already configured in `app/Config/Paytm.php`:
- Merchant ID: `hhdaGX54213527799734`
- Environment: `staging`
- Website: `WEBSTAGING`

### Test Payment Flow

1. Open: `http://localhost/Ind6TokenVendor/paytm_upi_test.html`
2. Enter amount: `10.00`
3. Click **"Pay with UPI"** or **"Pay with Gateway"**
4. Complete payment
5. Status auto-updates

**Note:** No real money is charged in staging mode!

---

## 🔐 Security

### Checksum Validation

All requests are secured with SHA-256 HMAC:

```php
$checksum = hash_hmac('sha256', $paramString, $merchantKey);
```

### Callback Verification

All callbacks are verified before processing:

```php
if (!$this->verifyChecksum($response, $checksum, $key)) {
    return $this->fail('Invalid checksum');
}
```

---

## 🚨 Troubleshooting

### Payment not updating?

**Check:**
1. Is polling running? (Check browser console)
2. Are credentials correct? (Check `app/Config/Paytm.php`)
3. Check logs: `writable/logs/log-*.php`

### QR code not working?

**Check:**
1. Is UPI VPA correct? (`abuzarmunshi12-2@okaxis`)
2. Try different UPI app
3. Check QR code data in browser console

### Callback not received?

**Check:**
1. Is callback URL accessible?
2. Check route: `app/Config/Routes.php`
3. Check logs for errors

---

## 📊 Monitoring

### Check Payment Status

```bash
# Via API
curl -X POST http://localhost/Ind6TokenVendor/api/paytm/check-status \
  -H "Content-Type: application/json" \
  -d '{"order_id": "PTM_ABC123"}'

# Via Database
SELECT * FROM payments WHERE status = 'pending' ORDER BY created_at DESC;
```

### View Logs

```bash
# Windows
type writable\logs\log-2023-12-23.php

# Linux/Mac
tail -f writable/logs/log-2023-12-23.php
```

---

## 🎓 Next Steps

1. **Test thoroughly** with different amounts
2. **Integrate into your app** using the API endpoints
3. **Customize UI** in `paytm_upi_test.html`
4. **Set up webhooks** for instant notifications
5. **Go live** by updating to production credentials

---

## 📚 Documentation

- **Full Guide:** `docs/PAYTM_UPI_GUIDE.md`
- **Paytm Docs:** https://developer.paytm.com/
- **UPI Spec:** https://www.npci.org.in/what-we-do/upi

---

## ✅ Checklist

Before going live:

- [ ] Update to production credentials
- [ ] Change environment to `production`
- [ ] Test with real small amounts
- [ ] Set up error monitoring
- [ ] Configure webhook URL
- [ ] Enable HTTPS
- [ ] Remove test routes

---

## 🆘 Support

**Having issues?**

1. Check `docs/PAYTM_UPI_GUIDE.md` for detailed docs
2. Review logs in `writable/logs/`
3. Check Paytm dashboard for transaction details
4. Verify database `payments` table

---

**Ready to accept payments?** 🎉

Open: `http://localhost/Ind6TokenVendor/paytm_upi_test.html`
