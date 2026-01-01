# Payraizen Integration - Troubleshooting Guide

## Error: "Unexpected token '<'"

This error means the API is returning HTML instead of JSON. Here's how to fix it:

### ✅ Solution Steps

#### 1. **Check Base URL Configuration**
The base URL in your CodeIgniter config is:
```
http://localhost/Ind6TokenVendor/
```

This means all API calls must include `/Ind6TokenVendor/` in the path.

**Correct URL:**
```
http://localhost/Ind6TokenVendor/api/payment/payraizen/initiate
```

**Wrong URL:**
```
http://localhost/api/payment/payraizen/initiate  ❌
```

#### 2. **Test Pages Available**

I've created two test pages for you:

**Option A - Simple Test (Recommended):**
```
http://localhost/Ind6TokenVendor/test_payraizen.html
```
- Uses hardcoded base URL
- Easier to debug
- Shows detailed console logs

**Option B - Dynamic Demo:**
```
http://localhost/Ind6TokenVendor/payraizen_demo.html
```
- Auto-detects base URL
- Prettier interface
- More production-ready

#### 3. **Test with cURL**

Open Command Prompt and run:

```bash
curl -X POST http://localhost/Ind6TokenVendor/api/payment/payraizen/initiate ^
  -H "Content-Type: application/json" ^
  -d "{\"vendor_id\":\"1\",\"amount\":100}"
```

**Expected Response:**
```json
{
  "success": true,
  "status": "initiated",
  "transaction_id": "TXN_...",
  "gateway_order_id": "...",
  "payment_url": "upi://pay?...",
  "payment_info": {
    "amount": 100,
    "currency": "INR"
  }
}
```

#### 4. **Check Apache mod_rewrite**

Ensure `mod_rewrite` is enabled in XAMPP:

1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find this line:
   ```
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Remove the `#` to uncomment it:
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Restart Apache

#### 5. **Check .htaccess**

Verify `.htaccess` exists in `public/` folder:
```
c:\xampp\htdocs\Ind6TokenVendor\public\.htaccess
```

It should contain:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([\s\S]*)$ index.php/$1 [L,NC,QSA]
```

#### 6. **Check Routes**

Verify routes exist in `app/Config/Routes.php`:
```php
$routes->post('api/payment/payraizen/initiate', 'PaymentApi::createPayraizenRequest');
$routes->post('api/payment/payraizen/webhook', 'PaymentApi::handlePayraizenWebhook');
```

#### 7. **Check Controller**

Verify `app/Controllers/PaymentApi.php` has the method:
```php
public function createPayraizenRequest()
{
    // ... method exists
}
```

---

## Common Errors & Solutions

### Error: "404 Not Found"
**Cause:** Route not configured or mod_rewrite not enabled

**Solution:**
1. Check Routes.php has the route defined
2. Enable mod_rewrite in Apache
3. Verify .htaccess exists

### Error: "Vendor not found"
**Cause:** Vendor ID doesn't exist in database

**Solution:**
1. Check `vendors` table has a record with the ID you're using
2. Try with vendor_id = 1
3. Create a test vendor if needed

### Error: "Payment initiation failed"
**Cause:** Payraizen API credentials are invalid or API is down

**Solution:**
1. Update credentials in `PaymentApi.php` (line ~212):
   ```php
   $merchantId = 'YOUR_REAL_MERCHANT_ID';
   $token = 'YOUR_REAL_API_TOKEN';
   ```
2. Check Payraizen API status
3. Review logs: `writable/logs/log-YYYY-MM-DD.log`

### Error: "Database error"
**Cause:** Migration not run or database connection issue

**Solution:**
1. Run migration:
   ```bash
   php spark migrate
   ```
2. Check database credentials in `.env` or `app/Config/Database.php`

---

## Debugging Checklist

- [ ] XAMPP is running
- [ ] Apache is started
- [ ] MySQL is started (if using database)
- [ ] mod_rewrite is enabled
- [ ] .htaccess exists in public folder
- [ ] Routes are defined in Routes.php
- [ ] Migration has been run
- [ ] Vendor exists in database
- [ ] Using correct base URL with /Ind6TokenVendor/
- [ ] Browser console shows no CORS errors
- [ ] PHP errors are logged in writable/logs/

---

## Quick Test Commands

### Test 1: Check if API endpoint exists
```bash
curl -I http://localhost/Ind6TokenVendor/api/payment/payraizen/initiate
```
**Expected:** HTTP 405 (Method Not Allowed) or 200
**Bad:** HTTP 404 (Not Found)

### Test 2: Test with actual POST
```bash
curl -X POST http://localhost/Ind6TokenVendor/api/payment/payraizen/initiate ^
  -H "Content-Type: application/json" ^
  -d "{\"vendor_id\":\"1\",\"amount\":100}"
```

### Test 3: Check PHP errors
```bash
type writable\logs\log-2025-12-13.log
```

---

## Browser Console Debugging

Open browser console (F12) and check:

1. **Network Tab:**
   - Request URL should include `/Ind6TokenVendor/`
   - Status should be 200 (not 404)
   - Response should be JSON (not HTML)

2. **Console Tab:**
   - Look for the logged API URL
   - Verify it matches: `http://localhost/Ind6TokenVendor/api/payment/payraizen/initiate`

---

## Still Not Working?

### Check PHP Error Log
```bash
type C:\xampp\apache\logs\error.log
```

### Enable CodeIgniter Debug Mode
In `.env` or `app/Config/Boot/development.php`:
```php
ini_set('display_errors', '1');
error_reporting(E_ALL);
```

### Test Simple Route
Add a test route in `Routes.php`:
```php
$routes->get('test', function() {
    return json_encode(['status' => 'ok', 'message' => 'Routes working!']);
});
```

Then visit: `http://localhost/Ind6TokenVendor/test`

---

## Success Indicators

✅ **Working correctly when:**
- Browser console shows: `Response status: 200`
- Response is valid JSON
- No "Unexpected token" errors
- Transaction ID is returned
- Payment URL is generated

---

## Next Steps After Fix

1. Update Payraizen credentials
2. Test with real payment
3. Configure webhook URL
4. Test webhook with Payraizen
5. Move to production

---

## Support Files

- **Simple Test:** `http://localhost/Ind6TokenVendor/test_payraizen.html`
- **Demo Page:** `http://localhost/Ind6TokenVendor/payraizen_demo.html`
- **PHP Test:** `http://localhost/Ind6TokenVendor/test_api.php`
- **Logs:** `writable/logs/log-YYYY-MM-DD.log`
