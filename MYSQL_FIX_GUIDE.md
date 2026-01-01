# MySQL Not Running - Quick Fix Guide

## ⚠️ Issue
MySQL is not running in XAMPP, so the migration cannot connect to the database.

---

## ✅ Solution (Choose One)

### **Option 1: Start MySQL via XAMPP Control Panel** (RECOMMENDED)

1. **Open XAMPP Control Panel**
   - Location: `C:\xampp\xampp-control.exe`
   - Or search "XAMPP" in Windows Start menu

2. **Start MySQL**
   - Click the **"Start"** button next to **MySQL**
   - Wait for it to turn green
   - Port should show: `3306`

3. **Run the Database Update**
   ```bash
   cd c:\xampp\htdocs\Ind6TokenVendor
   php spark migrate
   ```

---

### **Option 2: Manual SQL Update** (If XAMPP doesn't start)

If MySQL won't start in XAMPP, run the SQL manually:

1. **Start MySQL** (via XAMPP Control Panel)

2. **Open phpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`
   - Or click "Admin" next to MySQL in XAMPP

3. **Select Database**
   - Click on `ind6token_admin` database in left sidebar

4. **Run SQL**
   - Click the **"SQL"** tab at the top
   - Copy and paste this SQL:
   ```sql
   ALTER TABLE `payments` 
   ADD COLUMN `gateway_txn_id` VARCHAR(100) NULL AFTER `txn_id`;
   ```
   - Click **"Go"**

5. **Verify**
   - Click on `payments` table
   - Click "Structure" tab
   - You should see `gateway_txn_id` column

---

### **Option 3: Use SQL File** (Easiest)

1. **Start MySQL** in XAMPP Control Panel

2. **Open phpMyAdmin**: `http://localhost/phpmyadmin`

3. **Import SQL File**:
   - Select `ind6token_admin` database
   - Click **"Import"** tab
   - Click **"Choose File"**
   - Select: `c:\xampp\htdocs\Ind6TokenVendor\database_update.sql`
   - Click **"Go"**

---

## 🔧 Troubleshooting MySQL Won't Start

### **Issue: Port 3306 Already in Use**

**Check what's using port 3306:**
```bash
netstat -ano | findstr :3306
```

**Solution:**
1. Stop the conflicting service
2. Or change MySQL port in XAMPP config

### **Issue: MySQL Service Error**

**Solution:**
1. In XAMPP Control Panel, click **"Config"** next to MySQL
2. Select **"my.ini"**
3. Check for errors in configuration
4. Or reinstall XAMPP

### **Issue: Missing mysql.exe**

**Solution:**
- Reinstall XAMPP from: https://www.apachefriends.org/

---

## ✅ After MySQL is Running

Once MySQL starts successfully:

### **Option A: Run Migration**
```bash
cd c:\xampp\htdocs\Ind6TokenVendor
php spark migrate
```

### **Option B: Test Without Migration**

The PayRaizen integration will still work even without the `gateway_txn_id` column. It's optional for storing PayRaizen's transaction reference.

**Test the payment:**
1. Open: `http://localhost/Ind6TokenVendor/public/payment_test.html`
2. Enter amount: `100`
3. Click "Test Pay Now"

The system will work, but you'll see a warning about the missing column in logs.

---

## 🎯 Quick Summary

**Fastest Solution:**
1. ✅ Open XAMPP Control Panel
2. ✅ Click "Start" next to MySQL
3. ✅ Open phpMyAdmin: `http://localhost/phpmyadmin`
4. ✅ Select `ind6token_admin` database
5. ✅ Click "SQL" tab
6. ✅ Paste this and click "Go":
   ```sql
   ALTER TABLE `payments` ADD COLUMN `gateway_txn_id` VARCHAR(100) NULL AFTER `txn_id`;
   ```
7. ✅ Done! Test payment now.

---

## 📞 Still Having Issues?

If MySQL won't start at all:
1. Check XAMPP error logs: `C:\xampp\mysql\data\mysql_error.log`
2. Try restarting your computer
3. Reinstall XAMPP
4. Or use SQLite instead (let me know if you want to switch)

---

**Created**: 2025-12-12  
**Status**: Waiting for MySQL to start
