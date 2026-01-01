# Database Connection Fix - MAMP Configuration

## Problem Identified

Your application was loading the login page but **not connecting to the database** because:

1. **Wrong MySQL Port**: Database config was set to port `3306` (default MySQL port)
2. **MAMP uses port `8889`**: MAMP's MySQL runs on port 8889 by default
3. **Database doesn't exist**: The database `ind6token_admin` hasn't been created yet

## Why the Login Page Still Showed

The login page (`auth/login`) displays without database connection because:
- The `Auth::login()` method only returns a view - no database queries
- Database is only accessed when you **submit the login form**
- If you tried to login, you would get a database connection error

## Changes Made

### 1. Fixed Database Port Configuration
**File**: `app/Config/Database.php` (Line 44)

**Before:**
```php
'port' => 3306,
```

**After:**
```php
'port' => 8889,
```

---

## Setup Instructions

### Step 1: Create the Database

Run this command in your terminal:

```bash
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS ind6token_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

Or use **phpMyAdmin**:
1. Open: `http://localhost:8888/phpMyAdmin/`
2. Click "New" in the left sidebar
3. Database name: `ind6token_admin`
4. Collation: `utf8mb4_general_ci`
5. Click "Create"

---

### Step 2: Run Migrations

CodeIgniter has migration files that will create all necessary tables. Run:

```bash
cd /Applications/MAMP/htdocs/Ind6TokenVendor
php spark migrate
```

This will create the following tables:
- `admins` - Admin users
- `vendors` - Vendor accounts
- `payments` - Payment transactions
- `bank_details` - Bank account information

---

### Step 3: Create Test Admin/Vendor Account

After migrations, you need a user to login. Run:

```bash
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 ind6token_admin -e "
INSERT INTO vendors (name, email, password, wallet_address, created_at, updated_at) 
VALUES (
    'Admin User',
    'admin@ind6token.com',
    'password123',
    '0x0000000000000000000000000000000000000000',
    NOW(),
    NOW()
);
"
```

**Login Credentials:**
- Email: `admin@ind6token.com`
- Password: `password123`

---

## Verification Steps

### 1. Check Database Connection
```bash
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 -e "SHOW DATABASES;" | grep ind6token
```

**Expected Output:**
```
ind6token_admin
```

### 2. Check Tables Created
```bash
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 ind6token_admin -e "SHOW TABLES;"
```

**Expected Output:**
```
+---------------------------+
| Tables_in_ind6token_admin |
+---------------------------+
| admins                    |
| bank_details              |
| migrations                |
| payments                  |
| vendors                   |
+---------------------------+
```

### 3. Test Login
1. Go to: `http://localhost:8888/Ind6TokenVendor/auth/login`
2. Enter:
   - Email: `admin@ind6token.com`
   - Password: `password123`
3. Click "Sign in"
4. **Expected**: Redirect to dashboard with data

---

## MAMP Configuration Details

### MySQL Connection Settings
- **Host**: `localhost` or `127.0.0.1`
- **Port**: `8889`
- **Username**: `root`
- **Password**: `` (empty by default)
- **Database**: `ind6token_admin`

### MAMP Paths
- **MySQL Binary**: `/Applications/MAMP/Library/bin/mysql`
- **phpMyAdmin**: `http://localhost:8888/phpMyAdmin/`
- **Web Root**: `/Applications/MAMP/htdocs/`

---

## Troubleshooting

### Issue: "Connection refused" error
**Solution**: Make sure MAMP is running
1. Open MAMP application
2. Click "Start Servers"
3. Wait for both Apache and MySQL to turn green

### Issue: "Access denied for user 'root'"
**Solution**: Check MAMP's MySQL password
1. Open MAMP
2. Go to Preferences → MySQL
3. Note the root password (usually empty)
4. Update `app/Config/Database.php` if needed

### Issue: Migrations fail
**Solution**: Check if database exists first
```bash
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS ind6token_admin;"
```

### Issue: "Table 'vendors' doesn't exist"
**Solution**: Run migrations
```bash
php spark migrate
```

### Issue: Still can't login after setup
**Solution**: Verify user exists
```bash
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 ind6token_admin -e "SELECT * FROM vendors;"
```

---

## Migration Files Available

Your project has these migrations (in `app/Database/Migrations/`):

1. `CreateAdminsTable.php` - Creates admins table
2. `CreateInd6TokenTables.php` - Creates main tables (vendors, payments, bank_details)
3. `AddWalletAddressToVendors.php` - Adds wallet_address field
4. `AddGatewayFieldsToPayments.php` - Adds gateway fields
5. `AddPayraizenFieldsToPayments.php` - Adds PayRaizen integration fields
6. `AddCheckoutFieldsToPayments.php` - Adds checkout fields

All will be run automatically when you execute `php spark migrate`.

---

## Quick Setup Script

Run this complete setup in one go:

```bash
# Navigate to project
cd /Applications/MAMP/htdocs/Ind6TokenVendor

# Create database
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 -e "CREATE DATABASE IF NOT EXISTS ind6token_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

# Run migrations
php spark migrate

# Create test user
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 ind6token_admin -e "INSERT INTO vendors (name, email, password, wallet_address, created_at, updated_at) VALUES ('Admin User', 'admin@ind6token.com', 'password123', '0x0000000000000000000000000000000000000000', NOW(), NOW());"

# Verify setup
/Applications/MAMP/Library/bin/mysql -uroot -P8889 -h127.0.0.1 ind6token_admin -e "SELECT id, name, email FROM vendors;"

echo "Setup complete! You can now login at http://localhost:8888/Ind6TokenVendor/auth/login"
```

---

## What Happens After Database is Connected

Once the database is properly connected:

1. ✅ Login page will validate credentials against database
2. ✅ Dashboard will show real statistics (tokens sold, transactions, etc.)
3. ✅ Payment API endpoints will work
4. ✅ Vendor management will function
5. ✅ Transaction history will display

Without database connection:
- ❌ Login form submission fails
- ❌ Dashboard shows errors
- ❌ All database-dependent features fail
- ✅ Static pages (like login view) still load
