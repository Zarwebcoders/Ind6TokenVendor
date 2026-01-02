# PayRaizen Connection Error - Troubleshooting Guide

## Error Details
```
Failed to connect to partner.payraizen.com port 443 after 130483 ms: Could not connect to server
```

## Root Cause
The error indicates that your local MAMP server cannot establish a connection to the PayRaizen API endpoint. This is typically caused by:

1. **Network/Firewall blocking** - Your firewall or antivirus is blocking outgoing HTTPS connections
2. **SSL Certificate issues** - MAMP's PHP doesn't have proper SSL certificates configured
3. **DNS resolution problems** - Cannot resolve partner.payraizen.com to an IP address
4. **PayRaizen API is down** - The service might be temporarily unavailable

## Solutions Applied

### 1. Added Timeout Settings
✅ **File:** `/app/Controllers/PaymentApi.php`

Added connection and execution timeouts to prevent long hangs:
- Connection timeout: 30 seconds
- Total execution timeout: 60 seconds

### 2. Enhanced Error Logging
✅ **File:** `/app/Controllers/PaymentApi.php`

Now logs detailed cURL error information including:
- Error code and message
- Connection timing information
- DNS lookup time
- Total request time

### 3. Created Diagnostic Tool
✅ **File:** `/public/payraizen_diagnostic.php`

A comprehensive diagnostic page that tests:
- DNS resolution
- HTTPS connectivity
- SSL certificate verification
- API endpoint accessibility
- PHP configuration

## How to Diagnose

### Step 1: Run the Diagnostic Tool
1. Open your browser
2. Navigate to: `http://localhost:8888/Ind6TokenVendor/public/payraizen_diagnostic.php`
3. Review all test results

### Step 2: Interpret Results

**If DNS Resolution Fails:**
- Check your internet connection
- Try using Google DNS (8.8.8.8) or Cloudflare DNS (1.1.1.1)

**If Connection Times Out:**
- Check firewall settings (System Preferences > Security & Privacy > Firewall)
- Temporarily disable antivirus
- Ensure MAMP allows outgoing connections

**If SSL Verification Fails:**
- Update CA certificates
- Use the temporary SSL bypass (see below)

## Quick Fix Options

### Option A: Disable SSL Verification (Testing Only)
⚠️ **WARNING: Only use this for testing, never in production!**

Edit `/app/Controllers/PaymentApi.php` around line 368-370:

**Change from:**
```php
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
```

**Change to:**
```php
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
```

### Option B: Update CA Certificates (Recommended)

1. Download the latest CA certificates:
   ```bash
   curl -o /Applications/MAMP/conf/cacert.pem https://curl.se/ca/cacert.pem
   ```

2. Update your `php.ini` file:
   - Location: `/Applications/MAMP/bin/php/php8.x.x/conf/php.ini`
   - Find: `curl.cainfo`
   - Set to: `curl.cainfo = "/Applications/MAMP/conf/cacert.pem"`

3. Restart MAMP

### Option C: Check Firewall Settings

1. Open System Preferences > Security & Privacy > Firewall
2. Click "Firewall Options"
3. Ensure MAMP/Apache is allowed to accept incoming connections
4. Add MAMP to allowed apps if needed

### Option D: Test from Command Line

Test if you can reach PayRaizen from terminal:
```bash
curl -v https://partner.payraizen.com
```

If this works but PHP doesn't, it's a PHP/MAMP configuration issue.

## Verify API Credentials

Make sure you have valid PayRaizen credentials in `/app/Controllers/PaymentApi.php`:

```php
$merchantId = '987654321'; // Replace with your actual merchant ID
$token = 'bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK'; // Replace with your actual token
```

Contact PayRaizen support to verify:
1. Your merchant ID is active
2. Your API token is valid
3. The API endpoint URL is correct
4. Your IP address is whitelisted (if required)

## Next Steps

1. **Run the diagnostic tool** first: `/public/payraizen_diagnostic.php`
2. **Check the logs** at: `/writable/logs/log-[date].php`
3. **Try the SSL bypass** temporarily to see if it's an SSL issue
4. **Contact PayRaizen support** if the issue persists

## Alternative Testing

If you need to test the payment flow without PayRaizen connectivity:

1. Use the LocalPaisa gateway instead (if configured)
2. Use the manual UPI payment flow
3. Create a mock payment endpoint for testing

## Support

If none of these solutions work:
1. Check if PayRaizen API is operational
2. Try from a different network
3. Contact your network administrator
4. Reach out to PayRaizen technical support

---

**Last Updated:** 2026-01-03
**Status:** Diagnostic tools added, awaiting test results
