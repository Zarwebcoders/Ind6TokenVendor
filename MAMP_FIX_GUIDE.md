# MAMP Configuration Fix Guide

## Problem
Project not loading properly in MAMP on port 8888

## Solutions Applied

### 1. ✅ Fixed App Configuration
**File**: `app/Config/App.php`

```php
// Correct Configuration
public string $baseURL = 'http://localhost:8888/Ind6TokenVendor/';
public string $indexPage = 'index.php';
```

**Why?**
- `baseURL` should NOT include `index.php/` 
- `indexPage` should be set to `'index.php'` (not empty) when mod_rewrite is not working

### 2. Access URLs

#### With index.php (Current Setup)
```
Home: http://localhost:8888/Ind6TokenVendor/index.php
Login: http://localhost:8888/Ind6TokenVendor/index.php/auth/login
Test: http://localhost:8888/Ind6TokenVendor/index.php/payment/test
```

#### Without index.php (If mod_rewrite works)
```
Home: http://localhost:8888/Ind6TokenVendor/
Login: http://localhost:8888/Ind6TokenVendor/auth/login
Test: http://localhost:8888/Ind6TokenVendor/payment/test
```

## Common MAMP Issues & Fixes

### Issue 1: 404 Not Found
**Symptom**: All pages show 404 error

**Fix**:
1. Make sure you're accessing via `index.php`:
   ```
   http://localhost:8888/Ind6TokenVendor/index.php
   ```

2. Check if mod_rewrite is enabled in MAMP:
   - Open MAMP PRO (if you have it)
   - Go to Apache settings
   - Enable mod_rewrite

### Issue 2: Blank White Page
**Symptom**: Page loads but shows nothing

**Fix**:
1. Check PHP error logs:
   ```
   /Applications/MAMP/logs/php_error.log
   ```

2. Enable error display temporarily in `app/Config/Boot/development.php`:
   ```php
   ini_set('display_errors', '1');
   error_reporting(E_ALL);
   ```

3. Check writable folder permissions:
   ```bash
   chmod -R 777 /Applications/MAMP/htdocs/Ind6TokenVendor/writable
   ```

### Issue 3: Database Connection Error
**Symptom**: Error connecting to database

**Fix**:
1. Make sure MAMP MySQL is running
2. Check your `.env` file or `app/Config/Database.php`:
   ```php
   public array $default = [
       'hostname' => 'localhost',
       'username' => 'root',
       'password' => 'root',  // MAMP default
       'database' => 'your_database_name',
       'DBDriver' => 'MySQLi',
       'port'     => 8889,    // MAMP MySQL port (or 3306)
   ];
   ```

### Issue 4: CSS/JS Not Loading
**Symptom**: Page loads but no styling

**Fix**:
1. Update base_url() calls in views
2. Check browser console for 404 errors
3. Verify files exist in `public/` folder

### Issue 5: Routes Not Working
**Symptom**: Some routes work, others don't

**Fix**:
1. Clear route cache:
   ```bash
   cd /Applications/MAMP/htdocs/Ind6TokenVendor
   rm -rf writable/cache/*
   ```

2. Check `app/Config/Routes.php` for correct route definitions

## Quick Diagnostic Commands

### 1. Check PHP Version
```bash
/Applications/MAMP/bin/php/php8.2.0/bin/php -v
```

### 2. Check Apache Error Log
```bash
tail -f /Applications/MAMP/logs/apache_error.log
```

### 3. Check PHP Error Log
```bash
tail -f /Applications/MAMP/logs/php_error.log
```

### 4. Test Database Connection
```bash
/Applications/MAMP/Library/bin/mysql -u root -p
# Password: root (default)
```

### 5. Check File Permissions
```bash
ls -la /Applications/MAMP/htdocs/Ind6TokenVendor/writable
```

## Enable mod_rewrite (Optional)

If you want clean URLs without `index.php`:

### Step 1: Enable mod_rewrite in MAMP
1. Open `/Applications/MAMP/conf/apache/httpd.conf`
2. Find and uncomment:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Find `<Directory>` section and change:
   ```apache
   AllowOverride None
   ```
   to:
   ```apache
   AllowOverride All
   ```

### Step 2: Update App Config
```php
// app/Config/App.php
public string $indexPage = '';  // Empty string
```

### Step 3: Restart MAMP
Stop and start Apache in MAMP

## Testing Your Setup

### 1. Test Basic PHP
Create `test.php` in `/Applications/MAMP/htdocs/Ind6TokenVendor/public/`:
```php
<?php
phpinfo();
```
Access: `http://localhost:8888/Ind6TokenVendor/test.php`

### 2. Test CodeIgniter
Access: `http://localhost:8888/Ind6TokenVendor/index.php`

Should show login page or dashboard

### 3. Test Routes
Access: `http://localhost:8888/Ind6TokenVendor/index.php/payment/test`

Should show payment test page

## Current Configuration Status

✅ **baseURL**: `http://localhost:8888/Ind6TokenVendor/`  
✅ **indexPage**: `index.php`  
✅ **Port**: 8888 (MAMP default)  
✅ **.htaccess**: Present in root and public folders  

## Next Steps

1. **Access your project**:
   ```
   http://localhost:8888/Ind6TokenVendor/index.php
   ```

2. **If you see errors**, check:
   - Apache error log
   - PHP error log
   - Database connection
   - File permissions

3. **If page is blank**:
   - Enable error display
   - Check writable folder permissions
   - Clear cache

4. **For LocalPaisa testing**:
   ```
   http://localhost:8888/Ind6TokenVendor/index.php/payment/test
   ```

## MAMP Default Credentials

- **Apache Port**: 8888
- **MySQL Port**: 8889 (or 3306)
- **MySQL User**: root
- **MySQL Password**: root
- **Document Root**: `/Applications/MAMP/htdocs/`

## Still Having Issues?

1. **Restart MAMP completely**
2. **Clear browser cache**
3. **Check MAMP logs** for specific errors
4. **Verify PHP version** (should be 7.4 or higher)
5. **Check database exists** and is accessible

---

**Last Updated**: December 31, 2025  
**Status**: Configuration Fixed ✅
