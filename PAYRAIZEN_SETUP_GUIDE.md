# PayRaizen Integration - Setup Guide

## ✅ Integration Complete!

Your payment system now uses **PayRaizen Payment Gateway** for automatic payment verification.

---

## 🔧 **What Was Integrated:**

### 1. **PayRaizen API Configuration**
- **API Key**: `bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK`
- **Merchant ID (MID)**: `25`
- **Base URL**: `https://api.payraizen.com`

### 2. **New API Endpoints:**

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/payment/initiate` | POST | Create payment & get UPI intent from PayRaizen |
| `/api/payment/webhook` | POST | Receive automatic payment status from PayRaizen |
| `/api/payment/query` | POST | Manually check payment status |
| `/api/payment/update` | POST | Manual status update (legacy) |

### 3. **Automatic Features:**
✅ **Automatic Payment Verification** - No manual confirmation needed  
✅ **Webhook Support** - Real-time status updates  
✅ **No VPA Limits** - Works with merchant accounts  
✅ **Transaction Tracking** - Full audit trail  

---

## 📋 **Database Setup**

### Step 1: Start MySQL (XAMPP)
1. Open **XAMPP Control Panel**
2. Click **Start** next to **MySQL**
3. Wait for it to turn green

### Step 2: Run Migration
```bash
php spark migrate
```

This adds the `gateway_txn_id` column to the `payments` table.

---

## 🧪 **Testing the Integration**

### Option A: Use the Test Page

1. Open: `http://localhost/Ind6TokenVendor/public/payment_test.html`
2. Enter amount (e.g., `100`)
3. Click **"Test Pay Now"**
4. The system will:
   - Call PayRaizen API
   - Get UPI Intent link
   - Open your UPI app
5. Complete payment in UPI app
6. **PayRaizen will automatically update the status via webhook!**

### Option B: Test via API (Postman/cURL)

```bash
curl -X POST http://localhost/Ind6TokenVendor/api/payment/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "vendor_id": 1,
    "amount": 100
  }'
```

**Expected Response:**
```json
{
  "status": "initiated",
  "transaction_id": "TXN_...",
  "payment_info": {
    "amount": 100,
    "currency": "INR"
  },
  "upi_intent": "upi://pay?...",
  "gateway_data": {
    "order_id": "...",
    "intent_url": "..."
  }
}
```

---

## 🔗 **PayRaizen Webhook Configuration**

### Important: Configure Webhook URL in PayRaizen Dashboard

1. **Login to PayRaizen Merchant Dashboard**
2. Go to **Settings** → **Webhooks**
3. Set Webhook URL to:
   ```
   https://yourdomain.com/Ind6TokenVendor/api/payment/webhook
   ```
   
   **For local testing:**
   - Use **ngrok** to expose localhost:
     ```bash
     ngrok http 80
     ```
   - Copy the ngrok URL (e.g., `https://abc123.ngrok.io`)
   - Set webhook to: `https://abc123.ngrok.io/Ind6TokenVendor/api/payment/webhook`

4. **Save** the webhook configuration

---

## 📊 **How It Works**

```
User Initiates Payment
        ↓
Your API calls PayRaizen
        ↓
PayRaizen returns UPI Intent
        ↓
User opens UPI app & pays
        ↓
PayRaizen detects payment
        ↓
PayRaizen sends webhook to your server ✅
        ↓
Your database auto-updates ✅
        ↓
DONE! No manual confirmation needed!
```

---

## 🆘 **Troubleshooting**

### Issue: "Unable to connect to database"
**Solution:**
1. Start MySQL in XAMPP
2. Run `php spark migrate`

### Issue: "PayRaizen API error"
**Possible causes:**
- Invalid API key
- Incorrect MID
- Network/firewall blocking API calls

**Check:**
```bash
curl -X POST https://api.payraizen.com/api/collection/upi-intent \
  -H "Authorization: Bearer bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "email": "test@test.com",
    "mobile": "9999999999",
    "amount": "10",
    "mid": "25"
  }'
```

### Issue: "Webhook not receiving updates"
**Solutions:**
1. Check webhook URL is configured in PayRaizen dashboard
2. For local testing, use ngrok
3. Check webhook logs: `writable/logs/log-*.log`

### Issue: "Payment stuck in pending"
**Solution:**
Use the query endpoint to manually check status:
```bash
curl -X POST http://localhost/Ind6TokenVendor/api/payment/query \
  -H "Content-Type: application/json" \
  -d '{"transaction_id": "TXN_..."}'
```

---

## 💰 **PayRaizen Fees**

- **Transaction Fee**: ~2-3% (check your merchant agreement)
- **Settlement**: T+1 or T+2 days
- **No setup fees** (usually)

---

## 🔐 **Security Notes**

1. **API Key**: Currently hardcoded in `PaymentApi.php`
   - For production, move to `.env` file
   
2. **Webhook Verification**: 
   - PayRaizen may send a signature/hash
   - Verify webhook authenticity before processing

3. **HTTPS Required**:
   - PayRaizen webhooks require HTTPS in production
   - Use SSL certificate on your domain

---

## 📝 **Next Steps**

1. ✅ Start MySQL in XAMPP
2. ✅ Run `php spark migrate`
3. ✅ Test payment with `payment_test.html`
4. ✅ Configure webhook URL in PayRaizen dashboard
5. ✅ Test webhook with ngrok (for local)
6. ✅ Deploy to production with HTTPS

---

## 🎉 **Benefits Over Direct UPI**

| Feature | Direct UPI | PayRaizen |
|---------|-----------|-----------|
| **Auto Verification** | ❌ Manual | ✅ Automatic |
| **Transaction Fees** | Free | 2-3% |
| **Webhook Support** | ❌ No | ✅ Yes |
| **VPA Limits** | ₹2,000 collect | ✅ No limit |
| **Merchant Support** | ❌ Blocked | ✅ Works |
| **Settlement** | Instant | T+1/T+2 |
| **Fraud Protection** | ❌ No | ✅ Yes |

---

**Created**: 2025-12-12  
**Status**: ✅ Ready to Test  
**Support**: Check PayRaizen documentation or contact their support
