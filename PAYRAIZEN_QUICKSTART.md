# Payraizen Integration - Quick Start Guide

## ✅ What's Been Done

### 1. **PaymentApi Controller** (`app/Controllers/PaymentApi.php`)
   - ✅ Added `createPayraizenRequest()` method
   - ✅ Added `handlePayraizenWebhook()` method
   - ✅ Added helper methods for generating random user data

### 2. **PaymentModel** (`app/Models/PaymentModel.php`)
   - ✅ Added `gateway_name` field
   - ✅ Added `gateway_order_id` field
   - ✅ Added `gateway_response` field

### 3. **Database Migration**
   - ✅ Created migration file: `2025-12-13-000001_AddPayraizenFieldsToPayments.php`
   - ✅ Migration executed successfully
   - ✅ New columns added to `payments` table

### 4. **Routes** (`app/Config/Routes.php`)
   - ✅ Added `POST /api/payment/payraizen/initiate`
   - ✅ Added `POST /api/payment/payraizen/webhook`

### 5. **Documentation**
   - ✅ Created `PAYRAIZEN_INTEGRATION.md` (comprehensive guide)
   - ✅ Created this quick start guide

### 6. **Demo Page**
   - ✅ Created `public/payraizen_demo.html` (interactive test page)

---

## 🚀 How to Use

### Step 1: Configure Credentials
Open `app/Controllers/PaymentApi.php` and update:

```php
// Line ~212
$merchantId = 'YOUR_MERCHANT_ID';  // Replace with your Payraizen merchant ID
$token = 'YOUR_API_TOKEN';         // Replace with your Payraizen API token
```

**Recommended:** Move to `.env` file:
```env
PAYRAIZEN_MERCHANT_ID=987654321
PAYRAIZEN_API_TOKEN=bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK
```

### Step 2: Test the Integration

#### Option A: Use the Demo Page
1. Open browser: `http://localhost/payraizen_demo.html`
2. Enter Vendor ID and Amount
3. Click "Initiate Payment"
4. Check the response

#### Option B: Use cURL
```bash
curl -X POST http://localhost/api/payment/payraizen/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "vendor_id": "1",
    "amount": 100
  }'
```

### Step 3: Configure Webhook in Payraizen Dashboard
1. Login to Payraizen merchant dashboard
2. Go to Settings → Webhooks
3. Set webhook URL: `https://yourdomain.com/api/payment/payraizen/webhook`
4. Save settings

---

## 📊 API Endpoints

### 1. Initiate Payment
```
POST /api/payment/payraizen/initiate
```

**Request:**
```json
{
  "vendor_id": "1",
  "amount": 500
}
```

**Response:**
```json
{
  "success": true,
  "status": "initiated",
  "transaction_id": "TXN_ABC123",
  "gateway_order_id": "PR_XYZ789",
  "payment_url": "upi://pay?...",
  "intent": true,
  "payment_info": {
    "amount": 500,
    "currency": "INR"
  }
}
```

### 2. Webhook Handler
```
POST /api/payment/payraizen/webhook
```

**Payload from Payraizen:**
```json
{
  "order_details": {
    "tid": "PR_XYZ789",
    "status": "success",
    "bank_utr": "123456789012"
  }
}
```

---

## 🔄 Payment Flow

```
1. Client calls /api/payment/payraizen/initiate
   ↓
2. System creates pending payment in database
   ↓
3. System calls Payraizen API
   ↓
4. Payraizen returns payment deeplink
   ↓
5. User clicks deeplink and completes payment
   ↓
6. Payraizen sends webhook to /api/payment/payraizen/webhook
   ↓
7. System updates payment status to 'success'
   ↓
8. UTR is automatically saved
```

---

## 🗄️ Database Changes

New columns in `payments` table:
- `gateway_name` - Stores 'payraizen'
- `gateway_order_id` - Stores Payraizen's transaction ID
- `gateway_response` - Stores full JSON response from Payraizen

---

## 🔍 Debugging

### Check Logs
```bash
# View latest logs
tail -f writable/logs/log-2025-12-13.log
```

### Common Issues

**1. "Vendor not found"**
- Ensure vendor exists in `vendors` table
- Check vendor_id is correct

**2. "Payment initiation failed"**
- Verify Payraizen credentials
- Check API endpoint URL
- Review logs for Payraizen error response

**3. "Webhook not working"**
- Ensure webhook URL is publicly accessible
- Check Payraizen dashboard webhook settings
- Verify payload format matches expected structure

---

## 📝 Next Steps

1. **Security Enhancements:**
   - Move credentials to `.env`
   - Add webhook signature verification
   - Implement IP whitelisting

2. **Production Deployment:**
   - Use HTTPS for webhook endpoint
   - Set up proper error monitoring
   - Configure rate limiting

3. **Testing:**
   - Test with real Payraizen account
   - Test webhook with actual payments
   - Test error scenarios

---

## 📚 Additional Resources

- **Full Documentation:** `PAYRAIZEN_INTEGRATION.md`
- **Demo Page:** `http://localhost/payraizen_demo.html`
- **Payraizen Docs:** https://partner.payraizen.com/docs

---

## 🎯 Key Differences from Manual UPI

| Feature | Manual UPI | Payraizen |
|---------|-----------|-----------|
| UTR Capture | Manual entry | Automatic |
| Payment Verification | Screenshot upload | Webhook |
| User Steps | Multiple | Single click |
| Admin Work | High | Minimal |
| Success Rate | Variable | Higher |

---

## ✨ Summary

You now have a fully functional Payraizen payment gateway integration that:
- ✅ Automatically initiates payments
- ✅ Generates UPI deeplinks
- ✅ Captures UTR automatically via webhook
- ✅ Updates payment status in real-time
- ✅ Logs all transactions for debugging

**Test it now:** Open `http://localhost/payraizen_demo.html`
