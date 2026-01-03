#!/bin/bash
# Quick Fix Script for cPanel Git Pull .env Conflict
# Run this in your cPanel terminal when you see the .env merge error

echo "========================================="
echo "Fixing .env Git Pull Conflict"
echo "========================================="
echo ""

# Step 1: Backup production .env
echo "📦 Step 1: Backing up your production .env..."
cp .env .env.production.backup
echo "✅ Backup created: .env.production.backup"
echo ""

# Step 2: Stash the .env file
echo "💾 Step 2: Stashing .env file..."
git stash push .env
echo "✅ .env stashed"
echo ""

# Step 3: Pull latest changes
echo "⬇️  Step 3: Pulling latest changes from GitHub..."
git pull origin main
echo "✅ Pull completed"
echo ""

# Step 4: Restore production .env
echo "♻️  Step 4: Restoring your production .env..."
git stash pop
echo "✅ Production .env restored"
echo ""

# Step 5: Verify .env
echo "🔍 Step 5: Verifying .env file..."
if [ -f .env ]; then
    echo "✅ .env file exists"
    echo ""
    echo "Current .env settings:"
    echo "----------------------"
    grep -E "^app.baseURL|^CI_ENVIRONMENT|^PAYRAIZEN_VERIFY_SSL" .env
    echo ""
else
    echo "⚠️  Warning: .env file not found!"
    echo "Restoring from backup..."
    cp .env.production.backup .env
    echo "✅ Restored from backup"
fi

echo ""
echo "========================================="
echo "✅ Git Pull Fix Complete!"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Verify your .env has production settings"
echo "2. Clear cache: rm -rf writable/cache/*"
echo "3. Test your application"
echo ""
