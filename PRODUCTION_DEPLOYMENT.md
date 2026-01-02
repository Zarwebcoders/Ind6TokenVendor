# Production Deployment Guide - PayRaizen Integration

## ✅ Production-Ready Changes

The PayRaizen integration has been updated with **production-safe** SSL handling:

### What Changed:
1. **Environment-aware SSL verification** - Automatically verifies SSL in production
2. **Enhanced timeout settings** - Prevents long connection hangs
3. **Detailed error logging** - Better diagnostics for troubleshooting
4. **Diagnostic tools** - For testing connectivity issues

## 🚀 Deploying to Live Server

### Step 1: Ensure Production Environment is Configured

Your **live server** should have SSL verification **ENABLED** (default behavior).

**Do NOT add this to production `.env`:**
```env
# ❌ NEVER use this in production
PAYRAIZEN_VERIFY_SSL=false
```

### Step 2: Verify PayRaizen Credentials

In `/app/Controllers/PaymentApi.php` (lines 316-317), ensure you have **production credentials**:

```php
$merchantId = '987654321'; // ⚠️ Replace with ACTUAL production merchant ID
$token = 'bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK'; // ⚠️ Replace with ACTUAL production token
```

**IMPORTANT:** These should ideally be moved to `.env` file:
```env
PAYRAIZEN_MERCHANT_ID=your_actual_merchant_id
PAYRAIZEN_API_TOKEN=your_actual_api_token
```

### Step 3: Test on Live Server

After deployment, test the PayRaizen integration:

1. **Access the demo page** (if deployed):
   ```
   https://yourdomain.com/public/payraizen_demo.html
   ```

2. **Initiate a test payment** with a small amount (e.g., ₹10)

3. **Check logs** for any errors:
   ```
   /writable/logs/log-[date].log
   ```

### Step 4: Monitor for SSL Issues

If you encounter SSL errors on the **live server**, it means the server's CA certificates need updating.

**On your live server (via SSH):**

1. Check PHP cURL SSL info:
   ```bash
   php -r "print_r(curl_version());"
   ```

2. Update CA certificates (if needed):
   ```bash
   # For Ubuntu/Debian
   sudo apt-get update
   sudo apt-get install ca-certificates
   
   # For CentOS/RHEL
   sudo yum update ca-certificates
   ```

3. Restart web server:
   ```bash
   # Apache
   sudo service apache2 restart
   
   # Nginx + PHP-FPM
   sudo service nginx restart
   sudo service php-fpm restart
   ```

## 🏠 Local Development (MAMP)

For **local testing only**, if you encounter SSL issues with MAMP:

1. Add to your **local** `.env` file:
   ```env
   PAYRAIZEN_VERIFY_SSL=false
   ```

2. This will disable SSL verification **only in your local environment**

3. **Never commit this change to production!**

## 📋 Pre-Deployment Checklist

- [ ] Production PayRaizen credentials are configured
- [ ] `.env` file does NOT contain `PAYRAIZEN_VERIFY_SSL=false`
- [ ] Tested payment flow on staging/test environment
- [ ] Webhook URL is configured in PayRaizen dashboard
- [ ] Error logging is enabled and monitored
- [ ] Database has proper indexes on `gateway_order_id` column

## 🔒 Security Notes

### SSL Verification
- ✅ **Production**: SSL verification is ENABLED by default (secure)
- ⚠️ **Local Dev**: Can be disabled via environment variable (testing only)

### API Credentials
- Move merchant ID and token to `.env` file
- Never commit `.env` to Git
- Use different credentials for staging and production

### Webhook Security
- Verify webhook signatures (if PayRaizen provides them)
- Log all webhook payloads for audit trail
- Validate transaction amounts and status

## 🐛 Troubleshooting Production Issues

### Issue: "Connection timed out" on Live Server

**Possible Causes:**
1. Server firewall blocking outgoing HTTPS to PayRaizen
2. PayRaizen API is down or blocking your server IP
3. DNS resolution issues on server

**Solutions:**
1. Test connectivity from server:
   ```bash
   curl -v https://partner.payraizen.com
   ```

2. Check server firewall rules:
   ```bash
   sudo iptables -L -n | grep OUTPUT
   ```

3. Contact PayRaizen support to whitelist your server IP

### Issue: "SSL certificate problem" on Live Server

**Solution:**
Update CA certificates (see Step 4 above)

### Issue: "HTTP 401 Unauthorized"

**Cause:** Invalid API credentials

**Solution:**
1. Verify merchant ID and token with PayRaizen
2. Check if credentials are for production environment
3. Ensure token hasn't expired

### Issue: "HTTP 405 Method Not Allowed"

**Cause:** Incorrect API endpoint or HTTP method

**Solution:**
1. Verify API endpoint URL is correct
2. Ensure POST method is being used
3. Check PayRaizen API documentation for endpoint changes

## 📊 Monitoring

### What to Monitor:
1. **Payment success rate** - Track successful vs failed payments
2. **API response times** - Monitor for slow responses
3. **Error logs** - Watch for recurring errors
4. **Webhook delivery** - Ensure webhooks are being received

### Log Files:
- Application logs: `/writable/logs/log-[date].log`
- Web server logs: Check Apache/Nginx error logs
- PHP error logs: Check `php_error.log`

## 🔄 Rollback Plan

If issues occur after deployment:

1. **Revert to previous commit:**
   ```bash
   git revert HEAD
   git push origin main
   ```

2. **Or checkout previous stable version:**
   ```bash
   git checkout [previous-commit-hash]
   ```

## 📞 Support

**PayRaizen Support:**
- Check PayRaizen documentation
- Contact PayRaizen technical support
- Verify API status page

**Internal Support:**
- Check `/PAYRAIZEN_TROUBLESHOOTING.md` for detailed diagnostics
- Run `/public/payraizen_diagnostic.php` on server (if accessible)
- Review application logs

---

**Deployment Date:** 2026-01-03  
**Version:** 1.1.0  
**Status:** Production-Ready ✅
