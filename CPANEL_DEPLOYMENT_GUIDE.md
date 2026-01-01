# cPanel Deployment Guide for Ind6TokenVendor

## 🚀 Quick Fix for 403 Forbidden Error

The 403 error occurs because your `.htaccess` files were configured for local MAMP development with subdirectory paths. I've updated them for cPanel deployment.

## ✅ Changes Made

### 1. Root `.htaccess` Updated
- **Removed**: `/Ind6TokenVendor/` subdirectory references
- **Changed**: `RewriteCond %{REQUEST_URI} !^/Ind6TokenVendor/public/` → `!^/public/`
- **Changed**: `ErrorDocument 404 /Ind6TokenVendor/public/index.php` → `/public/index.php`

### 2. Public `.htaccess` Updated
- **Commented out**: `RewriteBase /Ind6TokenVendor/public/`
- Now uses default root-level routing

### 3. App.php Already Configured
- ✅ `baseURL` is set to: `https://ind6vendorfinal.zarwebcoders.in/`
- ✅ `indexPage` is empty (clean URLs enabled)

## 📋 cPanel Deployment Checklist

### Step 1: Upload Files to cPanel
You have two options:

#### Option A: Using Git (Recommended)
```bash
# SSH into your cPanel server
cd ~/public_html  # or your domain's root directory

# Clone your repository
git clone https://github.com/Zarwebcoders/Ind6TokenVendor.git .

# Or if already cloned, pull latest changes
git pull origin main
```

#### Option B: Using File Manager
1. Log into cPanel
2. Go to **File Manager**
3. Navigate to `public_html` (or your domain root)
4. Upload all files from your local project

### Step 2: Set Correct Document Root

**IMPORTANT**: Your document root must point to the `public` folder!

1. In cPanel, go to **Domains** or **Addon Domains**
2. Find your domain: `ind6vendorfinal.zarwebcoders.in`
3. Click **Manage** or **Edit**
4. Set **Document Root** to: `public_html/public` (or `/home/username/public_html/public`)
5. Save changes

### Step 3: Set File Permissions

```bash
# Set proper permissions
chmod 755 public
chmod 644 public/.htaccess
chmod 644 .htaccess
chmod -R 777 writable
```

Or via cPanel File Manager:
- Right-click on `writable` folder → **Change Permissions** → Set to `777`
- Right-click on `.htaccess` files → **Change Permissions** → Set to `644`

### Step 4: Configure Environment File

1. Rename `env` to `.env`:
```bash
mv env .env
```

2. Edit `.env` file with your production settings:
```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://ind6vendorfinal.zarwebcoders.in/'

# Database settings
database.default.hostname = localhost
database.default.database = your_database_name
database.default.username = your_db_username
database.default.password = your_db_password
database.default.DBDriver = MySQLi
```

### Step 5: Import Database

1. Go to **phpMyAdmin** in cPanel
2. Create a new database (or use existing)
3. Import your SQL files:
   - `database_update.sql`
   - `create_test_vendor.sql`
   - `verify_payments_table.sql`

### Step 6: Install Composer Dependencies

If you have SSH access:
```bash
cd ~/public_html
composer install --no-dev --optimize-autoloader
```

If no SSH access:
- Upload the `vendor` folder from your local machine

### Step 7: Verify .htaccess Files

Ensure these files exist and are readable:
- `/.htaccess` (root)
- `/public/.htaccess`

### Step 8: Test Your Application

Visit: `https://ind6vendorfinal.zarwebcoders.in/`

You should see your application homepage without 403 errors!

## 🔧 Troubleshooting

### Still Getting 403 Forbidden?

#### Check 1: Document Root
Make sure your domain points to the `public` folder, not the root project folder.

#### Check 2: File Permissions
```bash
# Directories should be 755
find . -type d -exec chmod 755 {} \;

# Files should be 644
find . -type f -exec chmod 644 {} \;

# Writable should be 777
chmod -R 777 writable
```

#### Check 3: Apache mod_rewrite
Ensure `mod_rewrite` is enabled. Contact your hosting provider if needed.

#### Check 4: .htaccess Override
Add this to the top of `public/.htaccess` if needed:
```apache
<IfModule mod_rewrite.c>
    Options +FollowSymlinks
    RewriteEngine On
</IfModule>
```

### Getting 500 Internal Server Error?

1. Check error logs in cPanel → **Error Log**
2. Ensure `writable` folder has 777 permissions
3. Check `.env` file exists and has correct database credentials
4. Verify PHP version is 7.4 or higher

### Database Connection Issues?

1. Verify database credentials in `.env`
2. Check if database user has proper privileges
3. Ensure database host is `localhost` (not `127.0.0.1`)

## 📝 Important Notes

### For Local Development (MAMP)
If you want to switch back to local development, you'll need to:

1. Update `app/Config/App.php`:
```php
public string $baseURL = 'http://localhost:8888/Ind6TokenVendor/';
```

2. Update root `.htaccess`:
```apache
RewriteCond %{REQUEST_URI} !^/Ind6TokenVendor/public/
```

3. Update `public/.htaccess`:
```apache
RewriteBase /Ind6TokenVendor/public/
```

### For Production (cPanel)
Keep the current configuration as updated.

## 🔐 Security Recommendations

1. **Remove sensitive files from public access**:
```apache
# Add to public/.htaccess
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

2. **Set environment to production** in `.env`:
```ini
CI_ENVIRONMENT = production
```

3. **Disable error display** in production:
```ini
app.showPHPErrors = false
```

4. **Enable HTTPS** (if SSL is installed):
```php
// In app/Config/App.php
public bool $forceGlobalSecureRequests = true;
```

## 📞 Need Help?

If you're still experiencing issues:
1. Check cPanel Error Logs
2. Enable debug mode temporarily in `.env`: `CI_ENVIRONMENT = development`
3. Check browser console for JavaScript errors
4. Verify all routes in `app/Config/Routes.php`

## ✨ Success Checklist

- [ ] Document root points to `public` folder
- [ ] `.htaccess` files updated (no subdirectory paths)
- [ ] File permissions set correctly
- [ ] `.env` file configured with production settings
- [ ] Database imported and connected
- [ ] Composer dependencies installed
- [ ] Application loads without 403 error
- [ ] Can access login page
- [ ] Can access dashboard after login

---

**Last Updated**: 2026-01-01
**Domain**: https://ind6vendorfinal.zarwebcoders.in/
