# 🌐 Paytm UPI - ngrok Public Access

## 🔗 Your Public URLs

### Main Application
```
https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor
```

### Paytm UPI Test Page
```
https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/paytm_upi_test.html
```

### API Endpoints

**Initiate UPI Payment:**
```
POST https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/api/paytm/upi/initiate
```

**Initiate Gateway Payment:**
```
POST https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/api/paytm/initiate
```

**Check Payment Status:**
```
POST https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/api/paytm/check-status
```

**Payment Callback:**
```
POST/GET https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/api/paytm/callback
```

---

## 📱 Test on Mobile

You can now test the Paytm UPI payment on your mobile device:

1. **Open on mobile:**
   ```
   https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/paytm_upi_test.html
   ```

2. **Click "Pay with UPI"**

3. **Scan QR code** or **click "Open UPI App"**

4. **Complete payment** in your UPI app

5. **Watch status update** in real-time!

---

## 🔧 ngrok Configuration

**Tunnel Details:**
- **Local Port:** 80 (XAMPP)
- **Protocol:** HTTPS
- **Public URL:** `https://eduardo-capillaceous-capitularly.ngrok-free.dev`
- **Status:** ✅ Active

**Command Used:**
```bash
ngrok http 80 --host-header="localhost"
```

---

## ⚠️ Important Notes

### ngrok Free Tier
- ✅ HTTPS enabled by default
- ⚠️ URL changes each time you restart ngrok
- ⚠️ Session expires after 2 hours (free tier)
- ⚠️ May show ngrok warning page on first visit (click "Visit Site")

### For Production
If you need a permanent URL:
1. Sign up for ngrok paid plan
2. Use a custom domain
3. Or deploy to a cloud server (AWS, DigitalOcean, etc.)

---

## 🧪 Testing with ngrok

### Test from Mobile Device

1. **Open URL on phone:**
   ```
   https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/paytm_upi_test.html
   ```

2. **Make a payment:**
   - Enter amount: ₹10.00
   - Click "Pay with UPI"
   - Scan QR code or click "Open UPI App"
   - Complete in your UPI app

3. **Verify:**
   - Status updates automatically
   - Payment saved in database
   - Check logs on your local server

### Test API from External Tool (Postman, curl)

```bash
curl -X POST https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/api/paytm/upi/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "vendor_id": 1,
    "amount": 10.00
  }'
```

---

## 🔍 Monitor ngrok Traffic

**ngrok Web Interface:**
```
http://localhost:4040
```

This shows:
- All HTTP requests
- Request/response details
- Replay requests
- Inspect traffic

---

## 🛠️ Troubleshooting

### ngrok Warning Page
If you see "You are about to visit..." warning:
- Click **"Visit Site"**
- This is normal for ngrok free tier
- Happens on first visit only

### URL Not Working
1. Check if ngrok is still running
2. Verify XAMPP is running
3. Test localhost first
4. Check ngrok dashboard: `http://localhost:4040`

### Callback Issues
If Paytm callbacks aren't working:
- Update callback URL in Paytm dashboard
- Use the ngrok HTTPS URL
- Ensure ngrok tunnel is active

---

## 📊 Update Paytm Config for ngrok

If you want Paytm to send callbacks to your ngrok URL, update the callback URL:

**In `app/Config/Paytm.php`:**
```php
public function getCallbackUrl()
{
    // For ngrok testing
    return 'https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/api/paytm/callback';
    
    // For production
    // return base_url('api/paytm/callback');
}
```

---

## 🎯 Quick Links

| Resource | URL |
|----------|-----|
| **Test Page** | https://eduardo-capillaceous-capitularly.ngrok-free.dev/Ind6TokenVendor/paytm_upi_test.html |
| **ngrok Dashboard** | http://localhost:4040 |
| **API Docs** | `docs/PAYTM_UPI_GUIDE.md` |
| **Local Test** | http://localhost/Ind6TokenVendor/paytm_upi_test.html |

---

## 🔄 Restart ngrok

If ngrok stops or you need a new URL:

```bash
# Stop current tunnel (Ctrl+C in the terminal)

# Start new tunnel
ngrok http 80 --host-header="localhost"

# Get new URL
curl http://localhost:4040/api/tunnels | ConvertFrom-Json | Select-Object -ExpandProperty tunnels | Select-Object public_url
```

---

## ✅ Status

**ngrok Tunnel:** 🟢 Active  
**Public URL:** https://eduardo-capillaceous-capitularly.ngrok-free.dev  
**Local Server:** http://localhost  
**Test Page:** ✅ Accessible  

---

**Share this URL to test from anywhere!** 🌍

**Created:** December 23, 2023  
**Expires:** When ngrok is stopped or after 2 hours (free tier)
