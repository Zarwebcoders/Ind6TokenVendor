#!/bin/bash

# Ind6TokenVendor - cPanel Deployment Script
# Run this script after uploading to cPanel

echo "========================================="
echo "Ind6TokenVendor Deployment Script"
echo "========================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Check PHP version
echo "Step 1: Checking PHP version..."
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
echo "PHP Version: $PHP_VERSION"

if (( $(echo "$PHP_VERSION >= 8.1" | bc -l) )); then
    echo -e "${GREEN}✓ PHP version is compatible${NC}"
else
    echo -e "${RED}✗ PHP version must be 8.1 or higher!${NC}"
    echo "Please update PHP version in cPanel"
    exit 1
fi
echo ""

# Step 2: Create .env if it doesn't exist
echo "Step 2: Checking .env file..."
if [ ! -f .env ]; then
    echo -e "${YELLOW}! .env file not found, creating from env template${NC}"
    cp env .env
    chmod 644 .env
    echo -e "${GREEN}✓ .env file created${NC}"
    echo -e "${YELLOW}! Please edit .env and update database credentials${NC}"
else
    echo -e "${GREEN}✓ .env file exists${NC}"
fi
echo ""

# Step 3: Set permissions
echo "Step 3: Setting file permissions..."
chmod 644 .htaccess 2>/dev/null
chmod 644 public/.htaccess 2>/dev/null
chmod 644 .env 2>/dev/null
chmod -R 777 writable
chmod 755 public
chmod 755 app
echo -e "${GREEN}✓ Permissions set${NC}"
echo ""

# Step 4: Check vendor folder
echo "Step 4: Checking Composer dependencies..."
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo -e "${YELLOW}! Vendor folder missing or incomplete${NC}"
    echo "Running composer install..."
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader
        echo -e "${GREEN}✓ Composer dependencies installed${NC}"
    else
        echo -e "${RED}✗ Composer not found!${NC}"
        echo "Please upload vendor folder from your local machine"
        echo "Or install Composer and run: composer install --no-dev"
    fi
else
    echo -e "${GREEN}✓ Vendor folder exists${NC}"
fi
echo ""

# Step 5: Check required directories
echo "Step 5: Checking directory structure..."
REQUIRED_DIRS=("app" "public" "writable" "vendor")
for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        echo -e "${GREEN}✓ $dir/ exists${NC}"
    else
        echo -e "${RED}✗ $dir/ is missing!${NC}"
    fi
done
echo ""

# Step 6: Check .htaccess files
echo "Step 6: Checking .htaccess files..."
if [ -f ".htaccess" ]; then
    echo -e "${GREEN}✓ Root .htaccess exists${NC}"
else
    echo -e "${RED}✗ Root .htaccess is missing!${NC}"
fi

if [ -f "public/.htaccess" ]; then
    echo -e "${GREEN}✓ public/.htaccess exists${NC}"
else
    echo -e "${RED}✗ public/.htaccess is missing!${NC}"
fi
echo ""

# Step 7: Check writable permissions
echo "Step 7: Verifying writable directory permissions..."
if [ -w "writable" ]; then
    echo -e "${GREEN}✓ writable/ is writable${NC}"
else
    echo -e "${RED}✗ writable/ is not writable!${NC}"
    chmod -R 777 writable
    echo "Fixed permissions"
fi
echo ""

# Step 8: Clear cache
echo "Step 8: Clearing cache..."
rm -rf writable/cache/*
rm -rf writable/debugbar/*
echo -e "${GREEN}✓ Cache cleared${NC}"
echo ""

# Summary
echo "========================================="
echo "Deployment Summary"
echo "========================================="
echo ""
echo "Next Steps:"
echo "1. Edit .env file with your database credentials"
echo "2. Make sure document root points to: public_html (or public_html/public)"
echo "3. Visit your site: https://ind6vendorfinal.zarwebcoders.in/"
echo ""
echo "If you see errors:"
echo "- Check error logs: tail -50 writable/logs/*.log"
echo "- Check cPanel Error Log: Metrics → Error Log"
echo "- See 500_ERROR_FIX.md for detailed troubleshooting"
echo ""
echo -e "${GREEN}Deployment script completed!${NC}"
