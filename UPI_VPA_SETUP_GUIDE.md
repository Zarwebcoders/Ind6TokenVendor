# UPI VPA Setup Guide - PERMANENT SOLUTION

## 🚨 Current Problem

You're getting **"Blocked VPA"** or **"Receiver not available"** errors because:

1. **Merchant VPAs** (like `paytmqr1xxjq0ss3y@paytm`) are **NOT designed** for direct UPI intent links
2. Payment apps (GPay, PhonePe) **block** merchant VPAs for security reasons
3. Merchant VPAs require official Payment Gateway integration (Paytm SDK/API)

---

## ✅ PERMANENT SOLUTION - Use a Personal UPI ID

### Option 1: Create Personal Paytm UPI ID (RECOMMENDED)

1. **Open Paytm App** on your phone
2. Go to **Profile** → **UPI & Payment Settings** → **Your UPI IDs**
3. Click **"Create New UPI ID"**
4. Choose a handle like: `yourname@paytm` or `yourbusiness@paytm`
5. **Copy this Personal UPI ID**
6. Update in your database: **Utilities → Bank Details → UPI ID**

✅ **This will work with all payment apps!**

---

### Option 2: Use Google Pay Personal VPA

1. Open **Google Pay** app
2. Go to **Profile** → **Settings** → **UPI ID**
3. Your UPI ID will be like: `yourname@okaxis` or `yourname@okhdfcbank`
4. **Copy this UPI ID**
5. Update in database: **Utilities → Bank Details → UPI ID**

✅ **Works perfectly with GPay, PhonePe, Paytm**

---

### Option 3: Use PhonePe Personal VPA

1. Open **PhonePe** app
2. Go to **Profile** → **Payment Settings** → **UPI ID**
3. Your UPI ID will be like: `yourname@ybl` or `yourname@ibl`
4. **Copy this UPI ID**
5. Update in database: **Utilities → Bank Details → UPI ID**

✅ **Works with all UPI apps**

---

## ⚠️ What NOT to Use

❌ **Merchant VPAs** - These will be BLOCKED:
- `paytmqr...@paytm` (Paytm QR Merchant)
- `merchant.xxxxx@paytm` (Paytm Merchant)
- Any VPA ending with `.merchant@...`

❌ **Business/Corporate VPAs** - May have restrictions:
- VPAs issued by payment gateways
- VPAs with special prefixes

---

## 🔧 How to Update in Your System

### Step 1: Login to Admin Dashboard
```
http://localhost/Ind6TokenVendor/admin/login
```

### Step 2: Go to Utilities → Bank Details

### Step 3: Update the Following Fields:
- **Account Holder Name**: Your actual name (as registered with UPI)
- **UPI ID**: Your PERSONAL UPI ID (from Option 1, 2, or 3 above)
- **Active**: ✅ Checked
- **Is Default**: ✅ Checked

### Step 4: Save Changes

---

## 🧪 Test the Payment

1. Open: `http://localhost/Ind6TokenVendor/public/payment_test.html`
2. Enter amount: `1.00`
3. Click **"Test Pay Now"**
4. Check the **VPA displayed** on screen
5. Click **"Open in UPI App"** or scan QR code
6. Complete payment in your UPI app

✅ **It should work without any "Blocked" or "Not Available" errors!**

---

## 📱 Why This Works

| VPA Type | Direct UPI Intent | Payment Apps Support | Recommended |
|----------|-------------------|---------------------|-------------|
| **Personal VPA** | ✅ YES | ✅ All Apps | ✅ **YES** |
| **Merchant VPA** | ❌ NO | ❌ Blocked | ❌ NO |
| **Gateway VPA** | ❌ NO | ⚠️ Limited | ❌ NO |

---

## 💰 Understanding UPI Limits

### ₹2,000 Collect Request Limit (SOLVED ✅)

**What is it?**
- Personal VPAs have a ₹2,000 limit for **UPI Collect Requests**
- This is an NPCI (National Payments Corporation of India) regulation
- You might see errors like:
  - PhonePe: "Up to ₹2000 limit"
  - Google Pay: "Exceeded bank limit, retry with smaller amount"

**How we solved it:**
- The code now uses `mode=02` parameter in the UPI intent
- This forces **PAYMENT mode** instead of **COLLECT REQUEST mode**
- **New limit: ₹1,00,000 (₹1 lakh) per transaction** ✅

### Daily Transaction Limits

| Limit Type | Amount | Count |
|-----------|--------|-------|
| **Per Transaction** | ₹1,00,000 | - |
| **Daily Total** | ₹1,00,000 | 20 transactions |
| **Collect Request** | ₹2,000 | 5 per day |

**Note:** With `mode=02`, you bypass the collect request limit entirely!

---

## 🆘 Still Having Issues?

### Check These:

1. **VPA Format**: Must be `username@provider` (e.g., `john@paytm`)
2. **Name Match**: Account holder name should match UPI registration
3. **Active Account**: UPI ID must be active and verified
4. **No Typos**: Double-check the UPI ID spelling

### Common Errors:

| Error Message | Cause | Solution |
|--------------|-------|----------|
| "Blocked VPA" | Using Merchant VPA | Use Personal VPA |
| "Receiver not available" | Name mismatch or inactive VPA | Check name & verify VPA is active |
| "Invalid VPA" | Typo in UPI ID | Verify spelling |
| "Transaction declined" | Insufficient balance | Check payer's balance |
| "Up to ₹2000 limit" (PhonePe) | Collect request limit | ✅ SOLVED - Code uses mode=02 |
| "Exceeded bank limit" (GPay) | Collect request limit | ✅ SOLVED - Code uses mode=02 |

---

## 💡 Pro Tips

1. **Use your most-used UPI app** for the VPA (better success rate)
2. **Keep the account holder name simple** (avoid special characters)
3. **Test with small amounts first** (₹1-10)
4. **Enable UPI notifications** to track payments

---

## 📞 Need Help?

If you've followed all steps and still face issues:

1. **Verify your Personal UPI ID** by sending ₹1 to yourself
2. **Check if the VPA is active** in your UPI app settings
3. **Try a different UPI app's VPA** (GPay, PhonePe, Paytm)

---

**Last Updated**: 2025-12-12
**Status**: ✅ Solution Implemented in Code
