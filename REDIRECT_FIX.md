# 🔧 Redirect Issue - Troubleshooting Guide

## Problem
When clicking "Create Test Payment", it redirects to the base URL instead of the checkout page.

## Possible Causes

### 1. No Vendor in Database ❌
**Check:**
```sql
SELECT * FROM vendors WHERE id = 1;
```

**Fix:**
```sql
INSERT INTO vendors (business_name, upi_id, created_at, updated_at)
VALUES ('Test Merchant', 'test@upi', NOW(), NOW());
```

Or run: `create_test_vendor.sql`

### 2. Payment Not Being Created ❌
**Check browser console:**
- Press F12
- Look for errors in Console tab
- Check Network tab for API calls

**Expected:**
```
POST /api/payment/test/create
Status: 200 OK
Response: {success: true, transaction_id: "TXN_xxx"}
```

### 3. Wrong Redirect URL ❌
**Check console logs:**
```javascript
// Should see in console:
Base URL: http://localhost:8080
Response: {success: true, transaction_id: "TXN_xxx"}
Redirecting to: http://localhost:8080/payment/checkout?txn_id=TXN_xxx
```

## 🔍 Debug Steps

### Step 1: Check Browser Console
```
1. Open test page: http://localhost:8080/payment/test
2. Press F12 (open DevTools)
3. Go to Console tab
4. Fill form and click "Create Test Payment"
5. Watch for:
   - Base URL log
   - Response log
   - Redirecting to log
   - Any error messages
```

### Step 2: Check Network Tab
```
1. Open DevTools (F12)
2. Go to Network tab
3. Click "Create Test Payment"
4. Look for:
   - POST to /api/payment/test/create
   - Status code (should be 200)
   - Response data
```

### Step 3: Check Server Logs
```
Location: writable/logs/log-2025-12-15.php

Look for:
- "Checkout page accessed with txn_id: TXN_xxx"
- "Payment lookup result: Found"
- "Vendor lookup for ID 1: Found"

If you see:
- "Payment not found" → Database issue
- "Vendor not found" → Need to create vendor
```

## ✅ Solutions

### Solution 1: Create Test Vendor
```bash
# Open phpMyAdmin or run SQL:
INSERT INTO vendors (business_name, upi_id, created_at, updated_at)
VALUES ('Test Merchant', 'test@upi', NOW(), NOW());
```

### Solution 2: Clear Browser Cache
```
Ctrl+Shift+Delete
→ Clear cached images and files
→ Clear for "All time"
```

### Solution 3: Restart Server
```bash
# Stop server (Ctrl+C)
# Start again
php spark serve
```

### Solution 4: Check Response
```javascript
// In browser console after clicking button:
// If you see error, check what it says
```

## 🧪 Test Manually

### Create Payment Manually
```sql
INSERT INTO payments (
    platform_txn_id,
    vendor_id,
    amount,
    status,
    buyer_name,
    buyer_email,
    buyer_phone,
    payment_method,
    created_at,
    updated_at
) VALUES (
    'TXN_TEST123',
    1,
    100.00,
    'pending',
    'Test User',
    'test@example.com',
    '9876543210',
    'upi',
    NOW(),
    NOW()
);
```

### Then Visit Checkout Directly
```
http://localhost:8080/payment/checkout?txn_id=TXN_TEST123
```

**Expected:** Should show checkout page with QR code

**If redirects to /payment/test:** Check logs for error message

## 📊 What Should Happen

### Correct Flow:
```
1. Fill form on /payment/test
   ↓
2. Click "Create Test Payment"
   ↓
3. AJAX POST to /api/payment/test/create
   ↓
4. Response: {success: true, transaction_id: "TXN_xxx"}
   ↓
5. Show success message
   ↓
6. Wait 2 seconds
   ↓
7. Redirect to: /payment/checkout?txn_id=TXN_xxx
   ↓
8. Show checkout page with QR code
```

### If It Redirects to Base URL:
```
Means one of these failed:
- Payment not created (check database)
- Vendor not found (create vendor)
- Transaction ID not passed correctly
```

## 🔍 Check Each Step

### 1. Vendor Exists?
```sql
SELECT * FROM vendors WHERE id = 1;
-- Should return 1 row
```

### 2. Payment Created?
```sql
SELECT * FROM payments ORDER BY created_at DESC LIMIT 1;
-- Should show your test payment
```

### 3. Console Shows Correct URL?
```javascript
// Should see in console:
Redirecting to: http://localhost:8080/payment/checkout?txn_id=TXN_xxx
// NOT: http://localhost:8080/
```

## 🎯 Quick Fix

**Most likely issue: No vendor in database**

```sql
-- Run this in phpMyAdmin:
INSERT INTO vendors (business_name, upi_id, created_at, updated_at)
VALUES ('Test Merchant', 'test@upi', NOW(), NOW());

-- Verify:
SELECT * FROM vendors;
```

Then try creating payment again!

## 📞 Still Not Working?

1. **Check browser console** - Any errors?
2. **Check server logs** - What does it say?
3. **Check database** - Vendor exists? Payment created?
4. **Try manual test** - Create payment via SQL, visit checkout URL directly

---

**Most Common Fix:** Create a vendor in the database! 🎯
