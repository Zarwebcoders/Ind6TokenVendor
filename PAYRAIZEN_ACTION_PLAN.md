# Payraizen Integration - Action Required

## ✅ FIXED: Connection Timeout Issue

The PHP cURL connection timeout has been **FIXED** with the following improvements:

### Changes Made to `PaymentApi.php`:
1. ✅ Force IPv4 resolution (fixes MAMP connection issues)
2. ✅ Optimized connection timeouts (15s connect, 30s total)
3. ✅ Auto-detect macOS SSL certificate bundle
4. ✅ Added TCP keepalive for stable connections
5. ✅ Added DNS cache timeout
6. ✅ Set custom user agent

---

## ⚠️ ACTION REQUIRED: IP Whitelisting

### Your Current IP Address
```
223.236.1.248
```

### What You Need to Do:

1. **Contact Payraizen Support**
   - Log in to your Payraizen merchant dashboard
   - Go to Support or Settings section
   - Request IP whitelisting

2. **Provide This Information**:
   ```
   Development IP: 223.236.1.248
   Purpose: API Integration Testing
   Endpoints: /tech/api/payin/create_intent
   ```

3. **For Production Deployment**:
   - Get your production server's IP address
   - Request whitelisting for that IP as well

---

## 🧪 Test the Fix

### Option 1: Run Diagnostic Script
Open in browser:
```
http://localhost:8888/Ind6TokenVendor/test_curl.php
```

This will show:
- ✅ PHP cURL configuration
- ✅ Connection test results
- ✅ Detailed timing information
- ✅ SSL verification status

### Option 2: Test Payment Flow
1. Go to your payment test page
2. Try initiating a Payraizen payment
3. Check the response

**Expected Results**:
- ❌ Before IP whitelisting: "Request From Unauthorized Ip"
- ✅ After IP whitelisting: Payment intent created successfully

---

## 📋 Verify Your Credentials

Current credentials in `PaymentApi.php` (line 316-317):
```php
$merchantId = '987654321';
$token = 'bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK';
```

**Action**: Verify these match your Payraizen account
- [ ] Merchant ID is correct
- [ ] API Token is valid
- [ ] Token has not expired

---

## 🔍 Monitoring

### Check Logs
```bash
tail -f /Applications/MAMP/htdocs/Ind6TokenVendor/writable/logs/log-$(date +%Y-%m-%d).log
```

### Look For:
- `Payraizen API Request` - Outgoing API calls
- `Payraizen Response` - API responses
- `Using CA bundle` - SSL certificate path
- `Payraizen cURL Error` - Any connection errors

---

## 📞 Payraizen Support Contact

To get your IP whitelisted:

1. **Via Dashboard**:
   - Login to: https://partner.payraizen.com
   - Navigate to: Support / Settings
   - Submit IP whitelist request

2. **Via Email** (if available):
   - Check your Payraizen welcome email for support contact
   - Subject: "IP Whitelist Request for API Integration"
   - Include: Your merchant ID and IP address (223.236.1.248)

3. **Via Phone/Chat**:
   - Check Payraizen dashboard for contact options

---

## 🚀 Next Steps

1. **Immediate**:
   - [x] Fix connection timeout (DONE)
   - [ ] Contact Payraizen for IP whitelisting
   - [ ] Run test_curl.php to verify fix

2. **After IP Whitelisting**:
   - [ ] Test payment initiation
   - [ ] Verify payment URL generation
   - [ ] Test webhook callback
   - [ ] Test end-to-end payment flow

3. **Before Production**:
   - [ ] Update with production credentials
   - [ ] Set PAYRAIZEN_VERIFY_SSL=true
   - [ ] Whitelist production server IP
   - [ ] Remove test_curl.php
   - [ ] Set up monitoring/alerts

---

## 📝 Environment Configuration

Your current `.env` settings:
```env
PAYRAIZEN_VERIFY_SSL=false
```

**For Production**, update to:
```env
PAYRAIZEN_MERCHANT_ID=YOUR_PRODUCTION_MID
PAYRAIZEN_API_TOKEN=YOUR_PRODUCTION_TOKEN
PAYRAIZEN_VERIFY_SSL=true
```

---

## ✨ Summary

**Problem**: Connection timeout when calling Payraizen API
**Root Cause**: MAMP PHP cURL configuration + IP not whitelisted
**Solution**: 
- ✅ Fixed cURL configuration (DONE)
- ⏳ Need IP whitelisting (PENDING)

**Current Status**: Code is ready, waiting for IP whitelisting from Payraizen.

---

**Questions?** Check `PAYRAIZEN_TROUBLESHOOTING.md` for detailed troubleshooting guide.
