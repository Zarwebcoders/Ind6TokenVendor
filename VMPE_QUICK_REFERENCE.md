# VMPE Payment Gateway - Quick Reference

## 🚀 Quick Start

### Test Page URL
```
http://localhost:8888/Ind6TokenVendor/payment/vmpe/test
```

### API Endpoints
| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/vmpe/initiate` | POST | Start payment |
| `/api/vmpe/webhook` | POST | Receive callback |
| `/api/vmpe/check-status` | POST | Check status |

## 📝 Configuration

### ✅ Credentials Configured
File: `/app/Controllers/VmpeGatewayApi.php`

```php
private $bearerToken = 'K6i4GOWCEvn69QZ8dCEgy9rRJFpw4yQD3WLnQRdb'; // ✅ Active
private $clientId = '121';
private $clientSecret = 'AGeEUnn22TRCIXb1DSkAsW93xGUEkilysCjB0iJe';
```

**Status**: Ready to test! 🚀

### Webhook URL for VMPE Dashboard
```
Production: https://yourdomain.com/api/vmpe/webhook
Local: http://localhost:8888/Ind6TokenVendor/api/vmpe/webhook
```

## 🧪 Test Payment

### cURL Example
```bash
curl -X POST http://localhost:8888/Ind6TokenVendor/api/vmpe/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "user": 1,
    "amount": 100,
    "payment_method": "upi_intent"
  }'
```

### JavaScript Example
```javascript
fetch('http://localhost:8888/Ind6TokenVendor/api/vmpe/initiate', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    user: 1,
    amount: 100,
    payment_method: 'upi_intent'
  })
})
.then(res => res.json())
.then(data => console.log(data));
```

## 📊 Payment Status Flow

```
pending → initiated → processing → success/failed
```

## 🔍 Debugging

### Check Logs
```bash
tail -f /Applications/MAMP/htdocs/Ind6TokenVendor/writable/logs/log-*.log
```

### Common Issues
1. **401 Unauthorized**: Check Bearer Token
2. **Connection Timeout**: Verify API URL
3. **Webhook Not Received**: Check URL configuration
4. **Status Not Updating**: Check database connection

## 📦 Files Created

```
✅ /app/Views/vmpe_test.php
✅ /app/Controllers/VmpeGatewayApi.php
✅ /app/Config/Routes.php (updated)
✅ /VMPE_IMPLEMENTATION_GUIDE.md
✅ /VMPE_QUICK_REFERENCE.md (this file)
```

## 🎨 Design Features

- Modern gradient purple/blue theme
- QR code modal for payments
- Real-time status polling (5s intervals)
- Smooth animations & transitions
- Mobile responsive
- Auto-redirect on success

## 💡 Tips

1. **Test First**: Always test with small amounts
2. **Monitor Logs**: Keep logs open during testing
3. **Webhook Testing**: Use tools like ngrok for local webhook testing
4. **Production Ready**: Update all credentials before going live
5. **Backup**: Keep database backups before major changes

## 🔐 Security Notes

- Never commit Bearer Token to git
- Use environment variables in production
- Enable HTTPS for production
- Validate all webhook data
- Implement rate limiting

## 📞 Support Contacts

- **VMPE Support**: support@vmpe.in
- **Documentation**: See VMPE_IMPLEMENTATION_GUIDE.md
- **Logs Location**: `/writable/logs/`

---
**Quick Access**: Bookmark this file for easy reference!
