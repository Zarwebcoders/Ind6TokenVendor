# 🚀 Quick Start Guide - Payment Checkout System

## ✅ What You Have Now

A complete payment checkout system with:
- ✨ Beautiful QR code display
- 📱 Mobile UPI app integration
- ⏱️ Auto payment detection (checks every 3 seconds)
- 🎉 Success/failure animations
- 📲 Works on desktop & mobile

## 🎯 How to Test (3 Simple Steps)

### Step 1: Start Your Server

```bash
cd c:\xampp\htdocs\Ind6TokenVendor
php spark serve
```

Or use XAMPP and visit: `http://localhost/Ind6TokenVendor`

### Step 2: Open Test Page

Visit: **http://localhost:8080/payment/test**

You'll see a form with:
- Amount (default: ₹100)
- Buyer details
- Vendor ID (default: 1)

### Step 3: Create & Test Payment

1. **Click "Create Test Payment"**
   - You'll be redirected to the checkout page
   - You'll see a QR code and timer

2. **Test Success (2 ways):**

   **Option A - Browser Console:**
   ```javascript
   $.post('/payment/simulate/success', {
       transaction_id: 'TXN_xxx' // Replace with your transaction ID
   });
   ```

   **Option B - New Tab:**
   - Open browser console (F12)
   - Run the simulation command above
   - Watch the checkout page auto-update!

3. **Test Failure:**
   ```javascript
   $.post('/payment/simulate/failure', {
       transaction_id: 'TXN_xxx'
   });
   ```

## 📱 Mobile Testing

### On Your Phone:

1. **Create payment on desktop**
2. **Open checkout URL on phone:**
   ```
   http://your-ip:8080/payment/checkout?txn_id=TXN_xxx
   ```
3. **Click "Pay with UPI" button**
4. **UPI app will open automatically!**
5. **Return to browser to see auto-update**

### Find Your IP:
```bash
# Windows
ipconfig

# Look for IPv4 Address (e.g., 192.168.1.100)
```

## 🎨 What Happens

### Desktop Flow:
```
1. User sees QR code
2. Scans with phone
3. Pays in UPI app
4. Page auto-detects payment (3s intervals)
5. Shows success animation 🎉
6. Auto-redirects to success page
```

### Mobile Flow:
```
1. User sees QR code + "Pay with UPI" button
2. Clicks button
3. UPI app opens automatically
4. User pays
5. Returns to browser
6. Page auto-detects payment
7. Shows success animation 🎉
8. Auto-redirects to success page
```

## 🧪 Testing Scenarios

### Scenario 1: Successful Payment
```bash
# Create payment
Visit: http://localhost:8080/payment/test

# Simulate success (in browser console on checkout page)
$.post('/payment/simulate/success', {
    transaction_id: 'TXN_xxx'
});

# Expected: Green checkmark, celebration effects, redirect to success page
```

### Scenario 2: Failed Payment
```bash
# Simulate failure
$.post('/payment/simulate/failure', {
    transaction_id: 'TXN_xxx'
});

# Expected: Red X, error message, redirect to failure page
```

### Scenario 3: Timeout
```bash
# Just wait 5 minutes on checkout page

# Expected: Timer reaches 0:00, shows timeout error
```

## 🔧 Quick Customization

### Change Timer Duration
Edit `app/Views/payment_checkout.php`:
```javascript
// Line ~200
let timeLeft = 5 * 60; // Change to 10 * 60 for 10 minutes
```

### Change Status Check Interval
```javascript
// Line ~300
const statusCheckInterval = setInterval(checkPaymentStatus, 3000);
// Change 3000 to 5000 for 5-second intervals
```

### Change Colors
```css
/* In payment_checkout.php <style> section */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
/* Change to your brand colors */
```

## 📊 Database Check

See payment status in database:
```sql
SELECT 
    platform_txn_id,
    amount,
    status,
    buyer_name,
    created_at,
    updated_at
FROM payments
ORDER BY created_at DESC
LIMIT 10;
```

## 🐛 Troubleshooting

### Problem: "Vendor not found"
**Solution:**
```sql
-- Check if vendor exists
SELECT * FROM vendors WHERE id = 1;

-- If not, create one
INSERT INTO vendors (business_name, upi_id, created_at) 
VALUES ('Test Merchant', 'test@upi', NOW());
```

### Problem: QR code not showing
**Solution:** Check internet connection (uses Google Charts API)

### Problem: Status not updating
**Solutions:**
1. Check browser console for errors
2. Verify transaction ID is correct
3. Check if database is updating:
   ```sql
   SELECT * FROM payments WHERE platform_txn_id = 'TXN_xxx';
   ```

### Problem: UPI app not opening on mobile
**Solutions:**
1. Ensure you're on mobile device
2. Check if UPI apps are installed
3. Try different browser (Chrome recommended)

## 📝 Important URLs

| Page | URL |
|------|-----|
| Test Page | `/payment/test` |
| Checkout | `/payment/checkout?txn_id=xxx` |
| Success | `/payment/success?txn=xxx` |
| Failure | `/payment/failure?txn=xxx` |
| Simulate Success | `POST /payment/simulate/success` |
| Simulate Failure | `POST /payment/simulate/failure` |

## 🎯 Next Steps

1. **Test on Mobile Device**
   - Use your phone to scan QR code
   - Test UPI app opening

2. **Customize Design**
   - Change colors to match your brand
   - Add your logo

3. **Integrate Real Gateway**
   - Connect to PayRaizen
   - Update webhook handler

4. **Add Notifications**
   - Send email on success
   - Send SMS confirmation

5. **Production Checklist**
   - Remove test routes
   - Add proper authentication
   - Enable HTTPS
   - Add rate limiting

## 💡 Pro Tips

1. **Keep browser console open** during testing to see AJAX requests
2. **Use Chrome DevTools** to simulate mobile devices
3. **Test with different amounts** to ensure formatting works
4. **Test timeout scenario** by waiting 5 minutes
5. **Check database** after each test to verify status updates

## 🎉 You're Ready!

Your payment system is now fully functional! 

**Test it now:**
1. Visit `http://localhost:8080/payment/test`
2. Create a payment
3. Simulate success/failure
4. Watch the magic happen! ✨

---

**Need help?** Check `PAYMENT_CHECKOUT_GUIDE.md` for detailed documentation.

**Questions?** Review the code comments in:
- `app/Controllers/PaymentCheckout.php`
- `app/Views/payment_checkout.php`
- `app/Controllers/PaymentWebhook.php`
