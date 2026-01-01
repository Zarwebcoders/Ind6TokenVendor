# 🔧 LocalPaisa IP Whitelist Error Fix

## 🎯 Error

```json
{
  "statusCode": "400",
  "statusMsg": "Invalid Ip Address",
  "dataContent": null
}
```

## ✅ What This Means

LocalPaisa requires you to **whitelist your server's IP address** in their merchant dashboard before they accept API requests.

---

## 🚀 How to Fix

### Step 1: Find Your Server's IP Address

#### Option A: Via PHP Script

Create this file on your server: `get_ip.php`

```php
<?php
echo "Server IP: " . $_SERVER['SERVER_ADDR'] . "\n";
echo "Public IP: " . file_get_contents('https://api.ipify.org') . "\n";
?>
```

Visit: `https://ind6vendorfinal.zarwebcoders.in/get_ip.php`

#### Option B: Via SSH

```bash
# Get server IP
curl ifconfig.me

# Or
curl ipinfo.io/ip

# Or check server details
hostname -I
```

#### Option C: Via cPanel

1. Login to cPanel
2. Look for **Server Information** or **General Information**
3. Note the **Shared IP Address** or **Dedicated IP Address**

---

### Step 2: Whitelist IP in LocalPaisa Dashboard

1. **Login to LocalPaisa Merchant Dashboard**
   - URL: https://localpaisa.com/merchant/login (or your merchant portal)

2. **Go to API Settings / IP Whitelist**
   - Usually under: Settings → API Configuration → IP Whitelist

3. **Add Your Server IP**
   - Add the IP address you found in Step 1
   - Format: `xxx.xxx.xxx.xxx`
   - Some panels allow CIDR notation: `xxx.xxx.xxx.xxx/32`

4. **Save Changes**

5. **Wait 5-10 minutes** for changes to propagate

---

## 📋 Common IP Scenarios

### Scenario 1: Shared Hosting (cPanel)
- Your server shares an IP with other sites
- Whitelist the **shared IP address** shown in cPanel

### Scenario 2: VPS/Dedicated Server
- You have a dedicated IP
- Whitelist your **public IP address**

### Scenario 3: Behind CloudFlare/Proxy
- Your server is behind a proxy
- You may need to whitelist **CloudFlare IP ranges**
- Or disable proxy for API requests

### Scenario 4: Dynamic IP
- If your IP changes frequently
- Contact LocalPaisa support to:
  - Whitelist an IP range
  - Or disable IP restriction (not recommended)

---

## 🔍 Quick IP Check Script

I'll create a diagnostic script for you:

```php
<?php
// Save as: public/check_ip.php

header('Content-Type: application/json');

$ipInfo = [
    'server_addr' => $_SERVER['SERVER_ADDR'] ?? 'Not available',
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'Not available',
    'http_x_forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'Not set',
    'http_client_ip' => $_SERVER['HTTP_CLIENT_IP'] ?? 'Not set',
];

// Get public IP
try {
    $publicIP = file_get_contents('https://api.ipify.org?format=json');
    $ipInfo['public_ip'] = json_decode($publicIP, true);
} catch (Exception $e) {
    $ipInfo['public_ip'] = 'Could not fetch';
}

// Get IP info
try {
    $ipDetails = file_get_contents('https://ipinfo.io/json');
    $ipInfo['ip_details'] = json_decode($ipDetails, true);
} catch (Exception $e) {
    $ipInfo['ip_details'] = 'Could not fetch';
}

echo json_encode($ipInfo, JSON_PRETTY_PRINT);
?>
```

Visit: `https://ind6vendorfinal.zarwebcoders.in/check_ip.php`

---

## 📝 What to Whitelist

Based on the response, whitelist these IPs in LocalPaisa:

1. **Primary**: The `public_ip` value
2. **Backup**: The `server_addr` value (if different)
3. **If behind proxy**: The `http_x_forwarded_for` value

---

## 🆘 Troubleshooting

### Issue: "Don't have access to LocalPaisa dashboard"

**Solution**: Contact LocalPaisa support:
- Email: support@localpaisa.com (or check their website)
- Provide:
  - Your merchant ID
  - Server IP address
  - Request to whitelist IP

### Issue: "IP keeps changing"

**Solution**: 
1. Use a static IP (upgrade hosting if needed)
2. Or ask LocalPaisa to disable IP restriction
3. Or whitelist an IP range

### Issue: "Multiple IPs showing"

**Solution**: Whitelist all of them:
- Server IP
- Public IP
- Any proxy IPs

### Issue: "Still getting error after whitelisting"

**Solution**:
1. Wait 10-15 minutes for changes to propagate
2. Clear any API caches
3. Verify IP was added correctly (no typos)
4. Check if IP restriction is enabled in LocalPaisa settings

---

## ✅ Verification Steps

After whitelisting:

1. **Wait 5-10 minutes**

2. **Test the API again**:
   - Visit: `https://ind6vendorfinal.zarwebcoders.in/payment/test`
   - Select LocalPaisa
   - Create test payment

3. **Check for success**:
   ```json
   {
     "success": true,
     "payment_url": "upi://pay?...",
     "transaction_id": "..."
   }
   ```

---

## 📞 LocalPaisa Support Info

If you need help from LocalPaisa:

**What to ask**:
> "I'm getting 'Invalid IP Address' error (400) when calling your API. 
> My server IP is: [YOUR_IP_HERE]
> Please whitelist this IP for my merchant account: [YOUR_MERCHANT_ID]"

**Include**:
- Your merchant ID
- Server IP address
- API endpoint you're calling
- Error message screenshot

---

## 🎯 Quick Summary

1. ✅ Find your server's IP address
2. ✅ Login to LocalPaisa merchant dashboard
3. ✅ Add IP to whitelist
4. ✅ Wait 5-10 minutes
5. ✅ Test payment again

---

## 📋 Checklist

- [ ] Found server IP address
- [ ] Logged into LocalPaisa dashboard
- [ ] Added IP to whitelist
- [ ] Saved changes
- [ ] Waited 10 minutes
- [ ] Tested payment again
- [ ] Got successful response

---

**The error is from LocalPaisa's security - just whitelist your IP and it will work!** 🚀
