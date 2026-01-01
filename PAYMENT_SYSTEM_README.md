# 💳 Payment Checkout System - README

## 🎯 Quick Overview

A complete UPI payment checkout system with:
- QR code display
- Auto payment detection
- Mobile UPI app integration
- Beautiful success/failure animations

## 🚀 Get Started in 30 Seconds

```bash
# 1. Start server
php spark serve

# 2. Open test page
http://localhost:8080/payment/test

# 3. Create payment & test!
```

## 📁 Important Files

| File | Purpose |
|------|---------|
| `QUICK_START.md` | **START HERE** - Testing guide |
| `IMPLEMENTATION_SUMMARY.md` | Complete overview |
| `PAYMENT_CHECKOUT_GUIDE.md` | Detailed documentation |

## 🧪 Test URLs

- **Test Page:** `/payment/test`
- **Checkout:** `/payment/checkout?txn_id=xxx`
- **Success:** `/payment/success?txn=xxx`
- **Failure:** `/payment/failure?txn=xxx`

## 🎨 Features

✅ QR Code Display  
✅ Auto Status Checking (every 3s)  
✅ Mobile UPI App Integration  
✅ 5-Minute Timer  
✅ Success/Failure Animations  
✅ Auto Redirect  

## 📱 Mobile Support

Works perfectly on:
- Desktop (scan QR with phone)
- Mobile (opens UPI apps directly)
- All UPI apps (GPay, PhonePe, Paytm, BHIM)

## 🔧 Quick Customization

### Change Timer
```javascript
// payment_checkout.php
let timeLeft = 5 * 60; // Change to 10 * 60
```

### Change Colors
```css
/* payment_checkout.php */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

## 🐛 Troubleshooting

**QR not showing?** Check internet (uses Google Charts)  
**Status not updating?** Check browser console  
**UPI not opening?** Test on real mobile device  

## 📚 Documentation

Read `QUICK_START.md` for step-by-step testing instructions.

## 🎉 That's It!

Your payment system is ready to use!

**Test now:** Visit `/payment/test` 🚀
