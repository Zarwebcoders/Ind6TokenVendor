#!/bin/bash

echo "================================================"
echo "EMERGENCY FIX SCRIPT - Ind6TokenVendor"
echo "================================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}This script will fix all common deployment issues${NC}"
echo ""

# Step 1: Check if we're in the right directory
if [ ! -f "app/Config/App.php" ]; then
    echo -e "${RED}ERROR: Not in project root directory!${NC}"
    echo "Please cd to your project root and run again"
    exit 1
fi

echo -e "${GREEN}✓ In correct directory${NC}"
echo ""

# Step 2: Create .env if missing
echo "Step 1: Checking .env file..."
if [ ! -f ".env" ]; then
    if [ -f "env" ]; then
        cp env .env
        echo -e "${GREEN}✓ Created .env from template${NC}"
    else
        echo -e "${RED}✗ env template file not found!${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}✓ .env exists${NC}"
fi

# Step 3: Fix all permissions
echo ""
echo "Step 2: Fixing permissions..."
chmod 755 . 2>/dev/null
chmod 644 .env 2>/dev/null
chmod 644 .htaccess 2>/dev/null
chmod 755 app 2>/dev/null
chmod 755 public 2>/dev/null
chmod 644 public/.htaccess 2>/dev/null
chmod 644 public/index.php 2>/dev/null
chmod 755 vendor 2>/dev/null
chmod -R 777 writable 2>/dev/null
echo -e "${GREEN}✓ Permissions fixed${NC}"

# Step 4: Clear all caches
echo ""
echo "Step 3: Clearing caches..."
rm -rf writable/cache/* 2>/dev/null
rm -rf writable/debugbar/* 2>/dev/null
rm -rf writable/session/* 2>/dev/null
echo -e "${GREEN}✓ Cache cleared${NC}"

# Step 5: Check vendor folder
echo ""
echo "Step 4: Checking Composer dependencies..."
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo -e "${YELLOW}! Vendor folder missing${NC}"
    if command -v composer &> /dev/null; then
        echo "Installing dependencies..."
        composer install --no-dev --optimize-autoloader --no-interaction
        echo -e "${GREEN}✓ Dependencies installed${NC}"
    else
        echo -e "${RED}✗ Composer not found!${NC}"
        echo "Please upload vendor folder from local machine"
        echo "Or install composer and run: composer install --no-dev"
    fi
else
    echo -e "${GREEN}✓ Vendor folder exists${NC}"
fi

# Step 6: Verify critical files
echo ""
echo "Step 5: Verifying critical files..."

CRITICAL_FILES=(
    "app/Config/App.php"
    "app/Config/Paths.php"
    "app/Config/Routes.php"
    "public/index.php"
    "public/.htaccess"
    ".htaccess"
)

ALL_OK=true
for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${RED}✗${NC} $file MISSING!"
        ALL_OK=false
    fi
done

if [ "$ALL_OK" = false ]; then
    echo ""
    echo -e "${RED}Some critical files are missing!${NC}"
    echo "Please ensure you've pulled all files from Git"
    exit 1
fi

# Step 7: Create index.html fallback
echo ""
echo "Step 6: Creating diagnostic files..."
cat > public/test.php << 'EOF'
<?php
phpinfo();
?>
EOF
chmod 644 public/test.php
echo -e "${GREEN}✓ Created test.php${NC}"

# Step 8: Check .htaccess content
echo ""
echo "Step 7: Verifying .htaccess..."
if grep -q "public" .htaccess; then
    echo -e "${GREEN}✓ Root .htaccess configured correctly${NC}"
else
    echo -e "${YELLOW}! Root .htaccess may need updating${NC}"
fi

# Step 9: Display current configuration
echo ""
echo "================================================"
echo "CONFIGURATION CHECK"
echo "================================================"
echo ""
echo "Document Root should be: $(pwd)"
echo "NOT: $(pwd)/public"
echo ""
echo "Current directory structure:"
ls -la | grep -E '^d|\.htaccess|\.env|index\.php'
echo ""

# Step 10: Final instructions
echo "================================================"
echo "NEXT STEPS"
echo "================================================"
echo ""
echo "1. Edit .env file:"
echo "   nano .env"
echo ""
echo "   Update these lines:"
echo "   database.default.database = your_db_name"
echo "   database.default.username = your_db_user"
echo "   database.default.password = your_db_pass"
echo ""
echo "2. Verify document root in cPanel:"
echo "   Should point to: public_html"
echo "   NOT: public_html/public"
echo ""
echo "3. Test your site:"
echo "   https://ind6vendorfinal.zarwebcoders.in/"
echo ""
echo "4. If still errors, upload and run diagnose.php:"
echo "   https://ind6vendorfinal.zarwebcoders.in/diagnose.php"
echo ""
echo "5. Check error logs:"
echo "   tail -50 writable/logs/*.log"
echo ""
echo -e "${GREEN}Emergency fix completed!${NC}"
echo ""
