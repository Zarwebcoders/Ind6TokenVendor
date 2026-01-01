# 🚨 COMPLETE FIX FOR HTTP 500 ERROR

## The Problem
You're seeing: "HTTP ERROR 500 - ind6vendorfinal.zarwebcoders.in is currently unable to handle this request"

## Root Cause
The application is trying to serve from root but paths are misconfigured, or document root is set incorrectly.

## ✅ SOLUTION - Updated Configuration

I've updated the configuration to work BOTH ways:
1. ✅ URLs show clean paths (no `/public`)
2. ✅ Actually serves from `public/` folder (for security)
3. ✅ Works regardless of document root setting

## 🚀 IMMEDIATE FIX - Do This Now!

### Option A: Keep Document Root as `public_html` (Recommended)

#### Step 1: Pull Latest Changes
```bash
cd ~/public_html
git pull origin main
```

#### Step 2: Set Correct Permissions
```bash
# Make .htaccess files readable
chmod 644 .htaccess
chmod 644 public/.htaccess

# Make writable directory writable
chmod -R 777 writable

# Make sure directories are accessible
chmod 755 public
chmod 755 app
chmod 755 vendor
```

#### Step 3: Verify .env File
```bash
# Check if .env exists
ls -la .env

# If it doesn't exist, copy from env
cp env .env
chmod 644 .env
```

#### Step 4: Edit .env File
Make sure these are set correctly:
```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://ind6vendorfinal.zarwebcoders.in/'

database.default.hostname = localhost
database.default.database = your_database_name
database.default.username = your_db_user
database.default.password = your_db_password
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

#### Step 5: Check PHP Version
```bash
php -v
```
Must be PHP 8.1 or higher!

#### Step 6: Test
Visit: `https://ind6vendorfinal.zarwebcoders.in/`

---

### Option B: Set Document Root to `public_html/public`

If you prefer the traditional CodeIgniter setup:

#### Step 1: Change Document Root in cPanel
1. Go to **Domains** → Find your domain
2. Click **Manage**
3. Set **Document Root** to: `public_html/public`
4. **Save**

#### Step 2: Pull Changes
```bash
cd ~/public_html
git pull origin main
```

#### Step 3: Set Permissions
```bash
chmod 644 public/.htaccess
chmod -R 777 writable
```

#### Step 4: Test
Visit: `https://ind6vendorfinal.zarwebcoders.in/`

---

## 🔍 Troubleshooting HTTP 500 Errors

### Check 1: View Error Logs
```bash
# Via SSH
tail -50 ~/public_html/writable/logs/*.log

# Or in cPanel
# Go to: Metrics → Error Log
```

### Check 2: Verify File Structure
```bash
ls -la ~/public_html/

# You should see:
# - .htaccess
# - app/
# - public/
# - vendor/
# - writable/
# - .env
```

### Check 3: Test PHP Syntax
```bash
php -l public/index.php
# Should say: "No syntax errors detected"
```

### Check 4: Check Composer Autoload
```bash
ls -la vendor/autoload.php
# File should exist
```

If vendor folder is missing:
```bash
composer install --no-dev --optimize-autoloader
```

### Check 5: Verify Database Connection
Create a test file: `~/public_html/public/db_test.php`
```php
<?php
$host = 'localhost';
$db = 'your_database_name';
$user = 'your_db_user';
$pass = 'your_db_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "✅ Database connection successful!";
} catch(PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>
```

Visit: `https://ind6vendorfinal.zarwebcoders.in/db_test.php`

### Check 6: PHP Extensions
```bash
php -m | grep -E 'intl|mbstring|json|mysqlnd|curl'
```

Required extensions:
- ✅ intl
- ✅ mbstring  
- ✅ json
- ✅ mysqlnd (or mysqli)
- ✅ curl

### Check 7: File Ownership
```bash
# Check who owns the files
ls -la ~/public_html/

# If needed, fix ownership (replace 'username' with your cPanel username)
chown -R username:username ~/public_html/
```

---

## 🔧 Common Issues & Solutions

### Issue: "Class 'Config\Paths' not found"

**Solution**: Composer autoload is missing
```bash
cd ~/public_html
composer install --no-dev
```

### Issue: "Failed to open stream: No such file or directory"

**Solution**: Check paths in `public/index.php`
```bash
cat public/index.php | grep "require"
# Should show: require FCPATH . '../app/Config/Paths.php';
```

### Issue: "Permission denied"

**Solution**: Fix permissions
```bash
chmod -R 755 ~/public_html
chmod -R 777 ~/public_html/writable
```

### Issue: Database connection failed

**Solution**: Update `.env` with correct credentials
```bash
nano .env
# Update database settings
```

### Issue: Blank white page

**Solution**: Enable error display temporarily
```bash
nano .env
# Change:
CI_ENVIRONMENT = development
```

Then check the page again to see actual errors.

---

## 📋 Complete Deployment Checklist

- [ ] Git pull completed successfully
- [ ] `.htaccess` exists in root directory
- [ ] `public/.htaccess` exists
- [ ] `.env` file exists and configured
- [ ] Database credentials are correct
- [ ] `writable/` folder has 777 permissions
- [ ] PHP version is 8.1 or higher
- [ ] `vendor/` folder exists with dependencies
- [ ] File ownership is correct
- [ ] Error logs checked for specific errors
- [ ] Site loads without 500 error

---

## 🎯 Quick Test Commands

Run these to verify everything:

```bash
# 1. Check PHP version
php -v

# 2. Check if .env exists
ls -la .env

# 3. Check permissions
ls -la writable/

# 4. Check .htaccess
cat .htaccess | head -20

# 5. Check vendor folder
ls -la vendor/autoload.php

# 6. Check recent errors
tail -20 writable/logs/*.log

# 7. Test PHP syntax
php -l public/index.php
```

---

## 📞 Still Not Working?

### Get Detailed Error Information

1. **Enable Development Mode**:
```bash
nano .env
# Set: CI_ENVIRONMENT = development
```

2. **Check Error Logs**:
```bash
tail -100 writable/logs/*.log
```

3. **Check Apache Error Log** (in cPanel):
   - Go to **Metrics** → **Error Log**
   - Look for the most recent errors

4. **Create a PHP Info Page**:
```bash
echo "<?php phpinfo(); ?>" > public/info.php
```
Visit: `https://ind6vendorfinal.zarwebcoders.in/info.php`

5. **Test Basic PHP**:
```bash
echo "<?php echo 'PHP Works!'; ?>" > public/test.php
```
Visit: `https://ind6vendorfinal.zarwebcoders.in/test.php`

---

## 🔐 Security Note

After fixing, remember to:
1. Set `CI_ENVIRONMENT = production` in `.env`
2. Delete test files (`db_test.php`, `info.php`, `test.php`)
3. Ensure `.env` is NOT publicly accessible

---

## ✅ Expected Result

After following these steps, visiting:
```
https://ind6vendorfinal.zarwebcoders.in/
```

Should show your application homepage, NOT a 500 error!

---

**Last Updated**: 2026-01-01  
**Status**: Ready to deploy  
**Next Step**: Pull changes and set permissions
