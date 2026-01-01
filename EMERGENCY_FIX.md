# 🚨 EMERGENCY FIX - HTTP 500 ERROR

## ⚡ IMMEDIATE SOLUTION - Run These Commands NOW!

On your cPanel server (SSH or Terminal):

```bash
cd ~/public_html
git pull origin main
bash emergency_fix.sh
```

Then:

```bash
nano .env
```

Update database credentials and save.

**Test**: `https://ind6vendorfinal.zarwebcoders.in/`

---

## 🔍 DIAGNOSE THE EXACT PROBLEM

### Upload diagnose.php to see what's wrong:

1. Upload `diagnose.php` to your cPanel `public_html` folder
2. Visit: `https://ind6vendorfinal.zarwebcoders.in/diagnose.php`
3. It will show you EXACTLY what's wrong
4. Follow the recommendations shown

---

## 🎯 MOST COMMON CAUSES OF HTTP 500

### 1. Missing .env File
```bash
cp env .env
chmod 644 .env
nano .env  # Update database credentials
```

### 2. Wrong Permissions
```bash
chmod -R 777 writable
chmod 644 .htaccess
chmod 644 public/.htaccess
```

### 3. Missing Composer Dependencies
```bash
composer install --no-dev
```

### 4. Wrong Document Root
In cPanel → Domains → Manage:
- Should be: `public_html`
- NOT: `public_html/public`

### 5. Database Connection Failed
```bash
nano .env
```
Update:
```ini
database.default.hostname = localhost
database.default.database = your_actual_db_name
database.default.username = your_actual_db_user
database.default.password = your_actual_db_password
```

---

## 📋 STEP-BY-STEP FIX

### Step 1: Pull Latest Code
```bash
cd ~/public_html
git pull origin main
```

### Step 2: Run Emergency Fix
```bash
bash emergency_fix.sh
```

This will:
- Create .env file
- Fix all permissions
- Clear cache
- Verify files
- Check dependencies

### Step 3: Configure Database
```bash
nano .env
```

Find these lines and update:
```ini
database.default.database = your_db_name
database.default.username = your_db_user
database.default.password = your_db_password
```

Save: `Ctrl+O`, `Enter`, `Ctrl+X`

### Step 4: Check Document Root
In cPanel:
1. Go to **Domains**
2. Click **Manage** next to your domain
3. Document Root should be: `public_html`
4. If it says `public_html/public`, change it to `public_html`
5. **Save**

### Step 5: Test
Visit: `https://ind6vendorfinal.zarwebcoders.in/`

---

## 🔍 CHECK ERROR LOGS

### Via SSH:
```bash
tail -50 writable/logs/*.log
```

### Via cPanel:
Go to: **Metrics** → **Error Log**

---

## 🆘 STILL NOT WORKING?

### Run Diagnostic Tool:

1. Upload `diagnose.php` to `public_html`
2. Visit: `https://ind6vendorfinal.zarwebcoders.in/diagnose.php`
3. It will show you exactly what's wrong
4. Follow the fix recommendations

### Check These:

```bash
# 1. PHP Version (must be 8.1+)
php -v

# 2. Check if .env exists
ls -la .env

# 3. Check if vendor exists
ls -la vendor/autoload.php

# 4. Check permissions
ls -la writable/

# 5. Test PHP works
echo "<?php echo 'PHP Works!'; ?>" > public/test.php
# Visit: https://ind6vendorfinal.zarwebcoders.in/test.php
```

---

## 💡 QUICK FIXES

### Fix 1: Recreate .env
```bash
rm .env
cp env .env
nano .env  # Update credentials
```

### Fix 2: Reinstall Dependencies
```bash
rm -rf vendor
composer install --no-dev
```

### Fix 3: Reset Permissions
```bash
chmod -R 755 .
chmod -R 777 writable
chmod 644 .env
chmod 644 .htaccess
```

### Fix 4: Clear Everything
```bash
rm -rf writable/cache/*
rm -rf writable/debugbar/*
rm -rf writable/session/*
```

---

## 📞 EMERGENCY CHECKLIST

- [ ] Pulled latest code: `git pull origin main`
- [ ] Ran emergency fix: `bash emergency_fix.sh`
- [ ] Created .env file: `cp env .env`
- [ ] Updated database credentials in .env
- [ ] Document root is `public_html` (not public_html/public)
- [ ] Permissions fixed: `chmod -R 777 writable`
- [ ] Vendor folder exists
- [ ] PHP version is 8.1+
- [ ] Checked error logs
- [ ] Ran diagnose.php

---

## 🎯 EXPECTED RESULT

After following these steps, visiting:
```
https://ind6vendorfinal.zarwebcoders.in/
```

Should show your application, NOT HTTP 500!

---

## 📝 AFTER IT WORKS

Delete diagnostic files:
```bash
rm diagnose.php
rm public/test.php
```

Set production mode:
```bash
nano .env
# Set: CI_ENVIRONMENT = production
```

---

**If you've done ALL of the above and still get errors, run diagnose.php and send me the output!**
