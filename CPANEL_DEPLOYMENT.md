# cPanel Deployment Guide - Handling .env File Conflicts

## Problem
When pulling from Git in cPanel, you see this error:
```
error: Your local changes to the following files would be overwritten by merge:
    .env
Please commit your changes or stash them before you merge.
Aborting
```

## Why This Happens
The `.env` file contains **environment-specific settings** (database credentials, API keys, etc.) that are different between:
- **Development** (local MAMP)
- **Production** (cPanel server)

The `.env` file should **never be tracked in Git** because it contains sensitive data.

---

## Solution: Fix the Git Pull Error in cPanel

### Option 1: Stash Your Production .env (Recommended)

Run these commands in cPanel Terminal or SSH:

```bash
# 1. Navigate to your project directory
cd ~/public_html  # or your project path

# 2. Stash your production .env file
git stash push .env

# 3. Pull the latest changes
git pull origin main

# 4. Restore your production .env file
git stash pop

# 5. Verify your .env is correct
cat .env
```

### Option 2: Keep Production .env Separate

```bash
# 1. Backup your production .env
cp .env .env.production.backup

# 2. Remove .env from staging
git reset HEAD .env

# 3. Pull the latest changes
git pull origin main

# 4. Restore your production .env
cp .env.production.backup .env

# 5. Verify
cat .env
```

### Option 3: Force Keep Production .env

```bash
# Tell Git to keep your local .env and ignore changes
git update-index --skip-worktree .env

# Now you can pull without conflicts
git pull origin main

# Your production .env will never be overwritten
```

---

## One-Time Setup for Future Deployments

### Step 1: Remove .env from Git History (Already Done)

This has been done in the latest commit. The `.env` file is now removed from Git tracking.

### Step 2: Configure Production .env

Your production `.env` should have these settings:

```env
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

app.baseURL = 'https://ind6vendorfinal.zarwebcoders.in/'
app.indexPage = ''
app.forceGlobalSecureRequests = true
app.CSPEnabled = false

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = localhost
database.default.database = YOUR_PRODUCTION_DB_NAME
database.default.username = YOUR_PRODUCTION_DB_USER
database.default.password = YOUR_PRODUCTION_DB_PASSWORD
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

#--------------------------------------------------------------------
# PAYRAIZEN CONFIGURATION
#--------------------------------------------------------------------

# IMPORTANT: Set to 'true' in production for security
PAYRAIZEN_VERIFY_SSL=true

# Optional: Specify CA bundle if needed
# CURL_CA_BUNDLE=/etc/ssl/cert.pem
```

### Step 3: Set Correct File Permissions

```bash
# Make .env readable only by owner
chmod 600 .env

# Verify permissions
ls -la .env
# Should show: -rw------- (600)
```

---

## Future Deployments

After the one-time setup, pulling updates is simple:

```bash
# Navigate to project
cd ~/public_html

# Pull latest changes (your .env won't be affected)
git pull origin main

# Clear CodeIgniter cache if needed
rm -rf writable/cache/*
```

---

## What Changed in Latest Commit

The latest commit (`a8e508d`) includes:

✅ **Fixed Files** (safe to pull):
- `app/Controllers/PaymentApi.php` - Enhanced cURL configuration
- `public/test_curl.php` - Diagnostic testing script
- `PAYRAIZEN_TROUBLESHOOTING.md` - Troubleshooting guide
- `PAYRAIZEN_ACTION_PLAN.md` - Action plan document

❌ **Removed from Git**:
- `.env` - No longer tracked (use `.env.example` as template)

✅ **New Template**:
- `.env.example` - Safe template for environment configuration

---

## Quick Reference: cPanel Git Pull Commands

### If you see the .env conflict error:

```bash
# Quick fix (keeps your production .env):
git stash push .env
git pull origin main
git stash pop
```

### To permanently prevent .env conflicts:

```bash
# Run once:
git update-index --skip-worktree .env

# Then always use:
git pull origin main
```

---

## Checklist After Pulling Updates

After running `git pull` in production:

- [ ] Verify `.env` has production settings (not development)
- [ ] Check `app.baseURL` is production URL
- [ ] Verify database credentials are correct
- [ ] Ensure `PAYRAIZEN_VERIFY_SSL=true` in production
- [ ] Clear cache: `rm -rf writable/cache/*`
- [ ] Check logs: `tail -f writable/logs/log-$(date +%Y-%m-%d).log`
- [ ] Test critical functionality

---

## Environment Differences

| Setting | Development (MAMP) | Production (cPanel) |
|---------|-------------------|---------------------|
| `CI_ENVIRONMENT` | development | production |
| `app.baseURL` | http://localhost:8888/... | https://ind6vendorfinal... |
| `app.forceGlobalSecureRequests` | false | true |
| `database.*` | Local DB | Production DB |
| `PAYRAIZEN_VERIFY_SSL` | false | **true** |

---

## Need Help?

If you encounter issues:

1. **Check Git status**: `git status`
2. **View differences**: `git diff .env`
3. **Restore from backup**: `cp .env.production.backup .env`
4. **Contact support** with error messages

---

## Security Notes

⚠️ **NEVER commit these to Git**:
- `.env` file
- Database passwords
- API keys/tokens
- Encryption keys
- Any sensitive credentials

✅ **Safe to commit**:
- `.env.example` (template with placeholders)
- Code files
- Documentation
- Configuration templates
