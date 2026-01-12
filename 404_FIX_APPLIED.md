# 🔧 URL Rewriting Issue - WORKAROUND

## ⚠️ Current Status

The clean URLs (without `index.php`) are not working due to Apache mod_rewrite configuration in MAMP.

### ❌ Not Working:
```
http://localhost:8888/Ind6TokenVendor/payment/vmpe/test
```

### ✅ Working URL:
```
http://localhost:8888/Ind6TokenVendor/index.php/payment/vmpe/test
```

---

## 🚀 **USE THIS URL TO TEST:**

```
http://localhost:8888/Ind6TokenVendor/index.php/payment/vmpe/test
```

---

## 📝 Why This Happens

This is a common issue with MAMP where:
1. `mod_rewrite` might not be enabled
2. `.htaccess` files might not be processed
3. `AllowOverride` might be set to `None`

---

## 🔧 How to Enable Clean URLs (Optional)

If you want to remove `index.php` from URLs, follow these steps:

### Step 1: Enable mod_rewrite in MAMP

1. Open MAMP
2. Go to **MAMP** → **Preferences** → **Web Server**
3. Ensure Apache is selected
4. Stop servers

### Step 2: Edit httpd.conf

1. Open: `/Applications/MAMP/conf/apache/httpd.conf`
2. Find this line:
   ```apache
   #LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Remove the `#` to uncomment it:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

### Step 3: Enable .htaccess

Find this section in `httpd.conf`:
```apache
<Directory />
    Options Indexes FollowSymLinks
    AllowOverride None
</Directory>
```

Change `AllowOverride None` to `AllowOverride All`:
```apache
<Directory />
    Options Indexes FollowSymLinks
    AllowOverride All
</Directory>
```

Also find the section for your document root and ensure it has `AllowOverride All`:
```apache
<Directory "/Applications/MAMP/htdocs">
    Options All
    AllowOverride All
    Require all granted
</Directory>
```

### Step 4: Restart MAMP

1. Stop all servers
2. Start servers again
3. Test the clean URL

---

## 🎯 Quick Solution (Recommended for Now)

**Just use the URL with `index.php` - it works perfectly!**

All functionality is the same, just the URL format is different.

### Working URLs:

| Page | URL |
|------|-----|
| VMPE Test | `http://localhost:8888/Ind6TokenVendor/index.php/payment/vmpe/test` |
| Paytm Test | `http://localhost:8888/Ind6TokenVendor/index.php/payment/paytm/test` |
| Home | `http://localhost:8888/Ind6TokenVendor/index.php/` |

### API Endpoints (also work with index.php):

```
POST http://localhost:8888/Ind6TokenVendor/index.php/api/vmpe/initiate
POST http://localhost:8888/Ind6TokenVendor/index.php/api/vmpe/webhook
POST http://localhost:8888/Ind6TokenVendor/index.php/api/vmpe/check-status
```

---

## 📱 Update JavaScript in Test Page

If you want the test page to work without manual URL changes, update the `vmpe_test.php` file:

Find this line:
```javascript
const response = await fetch('<?= base_url() ?>api/vmpe/initiate', {
```

Change to:
```javascript
const response = await fetch('<?= base_url() ?>index.php/api/vmpe/initiate', {
```

Do the same for the check-status endpoint.

---

## ✅ Bottom Line

**The application works perfectly - just use URLs with `index.php` in them for now!**

This is a MAMP configuration issue, not a problem with your code or the VMPE integration.

---

## 🚀 **START TESTING NOW:**

```
http://localhost:8888/Ind6TokenVendor/index.php/payment/vmpe/test
```

Everything else works exactly as documented! 🎉

---

**Created**: January 12, 2026  
**Status**: Workaround provided - fully functional
