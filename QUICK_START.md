# 🚀 QUICK START - Fix Your Site NOW!

## You're seeing HTTP 500 Error?

### Run These 3 Commands:

```bash
cd ~/public_html
git pull origin main
bash deploy.sh
```

That's it! The script will:
- ✅ Check PHP version
- ✅ Create .env file
- ✅ Set all permissions
- ✅ Verify directory structure
- ✅ Clear cache

## After Running deploy.sh:

### 1. Edit .env File
```bash
nano .env
```

Update these lines:
```ini
database.default.database = your_database_name
database.default.username = your_db_username  
database.default.password = your_db_password
```

Save: `Ctrl+O`, `Enter`, `Ctrl+X`

### 2. Test Your Site
Visit: `https://ind6vendorfinal.zarwebcoders.in/`

## Still Getting 500 Error?

### Check Error Logs:
```bash
tail -50 writable/logs/*.log
```

### Or in cPanel:
Go to: **Metrics** → **Error Log**

## Common Quick Fixes:

### Fix 1: Wrong Document Root
In cPanel → Domains → Manage:
- Set to: `public_html` (NOT `public_html/public`)

### Fix 2: Database Connection
```bash
nano .env
# Update database credentials
```

### Fix 3: Permissions
```bash
chmod -R 777 writable
chmod 644 .htaccess
chmod 644 public/.htaccess
```

### Fix 4: Missing Vendor
```bash
composer install --no-dev
```

## Need More Help?

See detailed guide: `500_ERROR_FIX.md`

---

**Quick Command Reference:**

```bash
# Pull latest code
git pull origin main

# Run deployment script
bash deploy.sh

# Edit environment
nano .env

# Check errors
tail -50 writable/logs/*.log

# Fix permissions
chmod -R 777 writable

# Test PHP
php -v
```

---

**Your site should work after these steps!** 🎉
