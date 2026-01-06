# cPanel Git Pull Fix - Quick Reference

## Problem
Error when pulling in cPanel:
```
error: Your local changes to the following files would be overwritten by merge:
.env
Please commit your changes or stash them before you merge.
```

## Quick Fix (Copy & Paste into cPanel Terminal)

```bash
# Navigate to your project directory
cd ~/public_html  # Adjust path if needed

# Backup your .env file
cp .env .env.backup

# Stash the .env changes
git stash push .env

# Pull the latest changes
git pull origin main

# Restore your .env from backup
cp .env.backup .env

# Clean up
rm .env.backup
```

## Alternative: One-Line Fix

```bash
git stash && git pull origin main && git stash drop
```

**Note:** This will temporarily hide your .env changes, pull updates, then discard the stashed changes (keeping your current .env intact).

## Permanent Solution

To prevent this issue in the future, run this **once** on your cPanel server:

```bash
# Remove .env from Git tracking (keeps the file, just stops tracking it)
git rm --cached .env

# Commit this change
git commit -m "Stop tracking .env file"

# Push to repository
git push origin main
```

After this, `.env` changes won't cause conflicts anymore.

## Verify It Worked

After pulling, verify your changes are there:

```bash
# Check recent commits
git log --oneline -5

# Verify files were updated
ls -la app/Controllers/PaymentApi.php
cat PAYRAIZEN_WEBHOOK_FIX.md
```

## If You Get Merge Conflicts

If you still get conflicts after `git stash pop`:

```bash
# Keep your version of .env
git checkout --ours .env

# Or keep the repository version
git checkout --theirs .env

# Then mark as resolved
git add .env
```

## Common cPanel Paths

- **Public HTML:** `~/public_html`
- **Subdomain:** `~/public_html/subdomain`
- **Addon Domain:** `~/addon-domain.com`

## Testing After Pull

1. **Check webhook endpoint:**
   ```bash
   curl https://ind6vendorfinal.zarwebcoders.in/api/payment/payraizen/test-webhook
   ```

2. **Check logs:**
   ```bash
   tail -50 writable/logs/log-$(date +%Y-%m-%d).log
   ```

3. **Verify file permissions:**
   ```bash
   chmod -R 755 app/
   chmod -R 777 writable/
   ```

## Need Help?

If the pull still fails:
1. Check what files have changes: `git status`
2. See what changed: `git diff`
3. Force reset (⚠️ DANGER - loses all local changes): `git reset --hard origin/main`
