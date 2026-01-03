# Payraizen Connection Troubleshooting Guide

## Error: Connection Timeout (Error Code 28)

### Problem
```
Failed to connect to payment gateway
Connection timed out after 30002 milliseconds
error_code: 28
```

### Root Causes & Solutions

#### 1. **MAMP cURL Configuration Issues** ✅ FIXED
**Problem**: MAMP's PHP cURL has different default settings than system cURL.

**Solution Applied**:
- ✅ Force IPv4 resolution: `CURLOPT_IPRESOLVE = CURL_IPRESOLVE_V4`
- ✅ Reduced connection timeout from 30s to 15s
- ✅ Added DNS cache timeout
- ✅ Enabled TCP keepalive
- ✅ Auto-detect macOS CA certificate bundle
- ✅ Set custom user agent

#### 2. **SSL Certificate Verification**
**Current Setting**: `PAYRAIZEN_VERIFY_SSL=false` in `.env`

**For Development**:
```env
PAYRAIZEN_VERIFY_SSL=false
```

**For Production** (Recommended):
```env
PAYRAIZEN_VERIFY_SSL=true
CURL_CA_BUNDLE=/etc/ssl/cert.pem
```

#### 3. **IP Whitelisting** ⚠️ ACTION REQUIRED
**Problem**: Payraizen requires your server IP to be whitelisted.

**Current Response**:
```json
{
  "status": "false",
  "msg": "Request From Unauthorized Ip : 223.236.xxx.xxx"
}
```

**Action Required**:
1. Contact Payraizen support
2. Provide your server IP: `223.236.xxx.xxx` (check current IP)
3. Request IP whitelisting for both:
   - Development IP (if testing locally)
   - Production server IP

**Check Your Current IP**:
```bash
curl -s https://api.ipify.org
```

#### 4. **Test Credentials**
Current credentials in code:
```php
$merchantId = '987654321';
$token = 'bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK';
```

**Verify**:
- [ ] Merchant ID is correct
- [ ] Token/API key is valid and not expired
- [ ] Credentials match your Payraizen account

---

## Testing Steps

### 1. Test PHP cURL Configuration
Visit: `http://localhost:8888/Ind6TokenVendor/test_curl.php`

This will show:
- PHP cURL version and configuration
- Connection test results
- Detailed timing information
- SSL verification status

### 2. Test from Command Line
```bash
# Test DNS resolution
nslookup partner.payraizen.com

# Test connectivity
ping -c 4 partner.payraizen.com

# Test API endpoint
curl -X POST https://partner.payraizen.com/tech/api/payin/create_intent \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","mobile":"9876543210","amount":100,"mid":"YOUR_MID"}' \
  -v
```

### 3. Check PHP Configuration
```bash
# Check PHP version
/Applications/MAMP/bin/php/php8.2.0/bin/php -v

# Check cURL support
/Applications/MAMP/bin/php/php8.2.0/bin/php -m | grep curl

# Check OpenSSL support
/Applications/MAMP/bin/php/php8.2.0/bin/php -m | grep openssl
```

---

## Environment Variables

Add to `.env` file:

```env
# Payraizen Configuration
PAYRAIZEN_MERCHANT_ID=987654321
PAYRAIZEN_API_TOKEN=bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK
PAYRAIZEN_API_URL=https://partner.payraizen.com/tech/api/payin/create_intent
PAYRAIZEN_VERIFY_SSL=false  # Set to 'true' in production

# SSL Certificate (optional, auto-detected)
# CURL_CA_BUNDLE=/etc/ssl/cert.pem
```

---

## Common Error Codes

| Error Code | Meaning | Solution |
|------------|---------|----------|
| 28 | Connection timeout | Check network, firewall, DNS |
| 6 | Could not resolve host | Check DNS settings |
| 7 | Failed to connect | Check firewall, server down |
| 35 | SSL connect error | Check SSL certificates |
| 51 | SSL peer certificate | Update CA bundle |
| 60 | SSL certificate problem | Verify SSL certificates or disable verification |

---

## Production Checklist

Before deploying to production:

- [ ] Update Payraizen credentials with production values
- [ ] Set `PAYRAIZEN_VERIFY_SSL=true`
- [ ] Whitelist production server IP with Payraizen
- [ ] Test payment flow end-to-end
- [ ] Set up webhook endpoint
- [ ] Configure proper error logging
- [ ] Set up monitoring/alerts for failed payments
- [ ] Remove test_curl.php from public directory

---

## Monitoring & Logs

### Check CodeIgniter Logs
```bash
tail -f writable/logs/log-$(date +%Y-%m-%d).log
```

### Look for:
- `Payraizen API Request` - Outgoing requests
- `Payraizen Response` - API responses
- `Payraizen cURL Error` - Connection errors
- `Using CA bundle` - SSL certificate path

---

## Support Contacts

**Payraizen Support**:
- Website: https://payraizen.com
- Support: Check your Payraizen dashboard for support contact

**Technical Issues**:
1. Check logs in `writable/logs/`
2. Run diagnostic script: `test_curl.php`
3. Verify network connectivity
4. Contact Payraizen support for IP whitelisting

---

## Quick Fix Summary

The connection timeout issue has been fixed by:

1. ✅ **Forcing IPv4**: Prevents IPv6 connection attempts that may fail
2. ✅ **Optimized Timeouts**: Reduced connection timeout to fail faster
3. ✅ **CA Bundle Auto-detection**: Automatically finds SSL certificates
4. ✅ **TCP Keepalive**: Maintains connection stability
5. ✅ **Better Error Logging**: Detailed error information for debugging

**Next Step**: Contact Payraizen to whitelist your IP address.
