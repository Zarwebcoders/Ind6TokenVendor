# 🔧 DATABASE FIX - Add Missing Column

## 🎯 Error

```
Failed to create payment: Unknown column 'platform_txn_id' in 'INSERT INTO'
```

## ✅ Solution

The `payments` table is missing the `platform_txn_id` column. This column stores the transaction ID from payment gateways (LocalPaisa, PayRaizen, etc.).

---

## 🚀 Quick Fix - Run This SQL

### Option 1: Using phpMyAdmin (Easiest)

1. **Login to cPanel**
2. **Go to phpMyAdmin**
3. **Select your database** (the one in your .env file)
4. **Click "SQL" tab**
5. **Paste this code:**

```sql
ALTER TABLE payments 
ADD COLUMN platform_txn_id VARCHAR(255) NULL AFTER id,
ADD INDEX idx_platform_txn_id (platform_txn_id);
```

6. **Click "Go"**
7. **Done!** ✅

---

### Option 2: Using SSH/Terminal

```bash
cd ~/public_html
mysql -u your_db_user -p your_db_name < add_platform_txn_id_simple.sql
```

Enter your database password when prompted.

---

## 📋 What This Does

### Adds Column:
- **Name**: `platform_txn_id`
- **Type**: VARCHAR(255)
- **Nullable**: Yes (NULL allowed)
- **Position**: After `id` column

### Adds Index:
- **Name**: `idx_platform_txn_id`
- **Purpose**: Faster lookups by transaction ID

---

## 🔍 Verify It Worked

After running the SQL, verify:

```sql
DESCRIBE payments;
```

You should see `platform_txn_id` in the column list.

---

## 📝 Complete Database Schema

After this fix, your `payments` table should have these columns:

```
id                  - INT (Primary Key)
platform_txn_id     - VARCHAR(255) (NEW!)
vendor_id           - INT
amount              - DECIMAL
status              - VARCHAR
buyer_name          - VARCHAR
buyer_email         - VARCHAR
buyer_phone         - VARCHAR
payment_method      - VARCHAR
gateway             - VARCHAR
created_at          - TIMESTAMP
updated_at          - TIMESTAMP
```

---

## 🎯 After Adding Column

### Test Your Payment Again:

1. Visit: `https://ind6vendorfinal.zarwebcoders.in/payment/test`
2. Select "LocalPaisa" gateway
3. Fill in test details
4. Click "Create Test Payment"
5. Should work now! ✅

---

## 🆘 If You Get Permission Error

If you see "Access denied" when running SQL:

### Check Database User Permissions:

1. **In cPanel → MySQL Databases**
2. **Find your database user**
3. **Make sure user has ALL PRIVILEGES on the database**
4. **Or use the database root user**

---

## 📦 Files Created

I've created two SQL migration files:

1. **`add_platform_txn_id_simple.sql`** - Simple version (use this)
2. **`add_platform_txn_id.sql`** - Advanced version with safety checks

Both are in your repository after `git pull`.

---

## ✅ Quick Steps Summary

```bash
# Step 1: Pull latest code
cd ~/public_html
git pull origin main

# Step 2: Run migration in phpMyAdmin
# (Copy SQL from add_platform_txn_id_simple.sql and run it)

# Step 3: Test payment
# Visit: https://ind6vendorfinal.zarwebcoders.in/payment/test
```

---

## 🎉 Expected Result

### Before:
```
❌ Unknown column 'platform_txn_id' in 'INSERT INTO'
```

### After:
```
✅ Payment created successfully!
✅ LocalPaisa payment initiated
✅ QR code generated
```

---

## 📞 Need Help?

### Can't Access phpMyAdmin?
- Use cPanel File Manager to upload and run SQL
- Or ask your hosting provider to run the migration

### Don't Know Database Name?
- Check your `.env` file
- Look for: `database.default.database = your_db_name`

### Forgot Database Password?
- Reset it in cPanel → MySQL Databases
- Update `.env` file with new password

---

**Just run that one SQL command in phpMyAdmin and your payment will work!** 🚀

```sql
ALTER TABLE payments 
ADD COLUMN platform_txn_id VARCHAR(255) NULL AFTER id,
ADD INDEX idx_platform_txn_id (platform_txn_id);
```
