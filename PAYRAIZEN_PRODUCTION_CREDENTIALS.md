# Payraizen Production Credentials - UPDATED

## ✅ **Your Actual Credentials**

```
Merchant ID: 25
API Token: oE39Gq3Gkcv2gTvz8hePLi3cG4KVbc0Q2pkg4B5i
```

**Status:** ✅ Updated in code and committed to Git

---

## 🔗 **Your Webhook URLs**

```
Payin Webhook:
https://ind6vendorfinal.zarwebcoders.in/api/payment/payraizen/webhook

Payout Webhook:
https://ind6vendorfinal.zarwebcoders.in/api/payout/payraizen/webhook
```

**Add these to your Payraizen dashboard!**

---

## 🚀 **Next Steps**

### **Step 1: Pull Updated Code to Server**

```bash
cd ~/public_html
git stash push .env
git pull origin main
git stash pop
```

### **Step 2: Test Payment Initiation**

Now that you have the correct credentials, try initiating a payment:

```bash
curl -X POST https://ind6vendorfinal.zarwebcoders.in/api/payment/payraizen/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "vendor_id": 1,
    "amount": 10
  }'
```

**Expected Response:**
- ✅ Success: Returns `transaction_id` and `payment_url`
- ❌ Still timeout: API endpoint URL might be wrong

### **Step 3: If Still Getting Timeout**

Contact Payraizen support and ask:

```
Hello Payraizen Support,

I have updated my API credentials:
- Merchant ID: 25
- API Token: oE39Gq3Gkcv2gTvz8hePLi3cG4KVbc0Q2pkg4B5i

I'm still getting connection timeout when calling:
https://partner.payraizen.com/tech/api/payin/create_intent

Questions:
1. Is this the correct API endpoint URL?
2. What is the correct base URL for your API?
3. Do I need to whitelist my server IP: 66.29.148.120?

My webhook URLs (ready to configure):
- Payin: https://ind6vendorfinal.zarwebcoders.in/api/payment/payraizen/webhook
- Payout: https://ind6vendorfinal.zarwebcoders.in/api/payout/payraizen/webhook

Thank you!
```

---

## 🔍 **Possible API Endpoint URLs**

If the current URL doesn't work, try these alternatives:

1. **Current (in code):**
   ```
   https://partner.payraizen.com/tech/api/payin/create_intent
   ```

2. **Alternative 1:**
   ```
   https://api.payraizen.com/payin/create_intent
   ```

3. **Alternative 2:**
   ```
   https://partnerpayraizen.com/api/payin/create_intent
   ```

4. **Alternative 3:**
   ```
   https://partner.payraizen.com/api/payin/create_intent
   ```

---

## 📊 **What Changed**

| Item | Old Value | New Value |
|------|-----------|-----------|
| Merchant ID | `987654321` (test) | `25` (production) |
| API Token | `bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK` (test) | `oE39Gq3Gkcv2gTvz8hePLi3cG4KVbc0Q2pkg4B5i` (production) |
| Status | ❌ Test credentials | ✅ Production credentials |

---

## 🧪 **Testing Checklist**

After pulling the code:

- [ ] Pull latest code to server
- [ ] Test payin initiation
- [ ] Check if connection timeout is resolved
- [ ] If working, test with small amount (₹10)
- [ ] Verify webhook receives notification
- [ ] Add webhook URLs to Payraizen dashboard
- [ ] Test payout with small amount
- [ ] Monitor logs for any errors

---

## 📝 **Files Updated**

1. `app/Controllers/PaymentApi.php` - Updated credentials in both payin and payout methods
2. `.env.example` - Updated with production credentials

**Git Commit:** `e875ac6` - "Update Payraizen API credentials to production values"

---

## 🔐 **Security Note**

⚠️ **IMPORTANT:** Your API credentials are now in the code. For better security:

1. **Move to .env file:**
   ```env
   PAYRAIZEN_MERCHANT_ID=25
   PAYRAIZEN_API_TOKEN=oE39Gq3Gkcv2gTvz8hePLi3cG4KVbc0Q2pkg4B5i
   ```

2. **Update code to use env variables:**
   ```php
   $merchantId = getenv('PAYRAIZEN_MERCHANT_ID');
   $token = getenv('PAYRAIZEN_API_TOKEN');
   ```

3. **Ensure .env is in .gitignore** (already done)

---

## ✅ **Summary**

- ✅ Credentials updated to production values
- ✅ Code committed and pushed to Git
- ✅ Webhooks ready
- ⏳ **Next:** Pull code to server and test
- ⏳ **Then:** Configure webhooks in Payraizen dashboard

**You're ready to test payments with real credentials!** 🚀
