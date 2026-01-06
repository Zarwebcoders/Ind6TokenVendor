# Quick Testing Guide for Payraizen Webhook

## 1. Access the Test Endpoint

Open your browser and visit:
```
https://ind6vendorfinal.zarwebcoders.in/api/payment/payraizen/test-webhook
```

This will show you:
- ✅ Your webhook URL
- ✅ Recent Payraizen payments
- ✅ A test payload
- ✅ A ready-to-use curl command

## 2. Test the Webhook

Copy the curl command from the test endpoint response and run it in your terminal.

Or use this generic test:

```bash
curl -X POST https://ind6vendorfinal.zarwebcoders.in/api/payment/payraizen/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "status": "true",
    "msg": "Payin Webhook",
    "order_details": {
      "amount": 516,
      "bank_utr": "TEST_451219086033",
      "status": "success",
      "tid": "PAYRIPS25260103125554369611",
      "mid": "lysCjB0iJe",
      "payee_vpa": "UPI"
    }
  }'
```

## 3. Check the Response

You should get a response like:
```json
{
  "status": "success",
  "message": "Payment status updated successfully",
  "transaction_id": "TXN_XXXXX"
}
```

## 4. Monitor Logs

In a separate terminal, watch the logs in real-time:

```bash
tail -f /Applications/MAMP/htdocs/Ind6TokenVendor/writable/logs/log-$(date +%Y-%m-%d).log | grep -i payraizen
```

## 5. Check Database

Verify the payment was updated:

```sql
SELECT 
    id,
    txn_id,
    gateway_order_id,
    amount,
    status,
    utr,
    created_at,
    updated_at,
    completed_time
FROM payments 
WHERE gateway_name = 'payraizen'
ORDER BY created_at DESC 
LIMIT 5;
```

## Expected Log Messages

✅ **Success Flow:**
```
Payraizen Webhook Raw Input: {...}
Payraizen Webhook Received {...}
Payraizen Webhook Processing: TID=..., Status=success, UTR=...
Payment updated successfully - TXN: TXN_XXX, Status: success, UTR: ...
```

⚠️ **Payment Not Found (will use fallback):**
```
Payraizen Webhook Error: Payment not found for gateway order ID: ...
Payraizen Webhook: Found payment by amount matching - TXN: ...
Payment updated successfully - TXN: TXN_XXX, Status: success, UTR: ...
```

❌ **Error (but still responds):**
```
Payraizen Webhook Error: ...
Webhook acknowledged with error
```

## Troubleshooting

### Issue: "Payment not found"
**Solution:** The webhook is working, but the payment record doesn't exist or the `gateway_order_id` doesn't match. Check:
1. Did the payment initiation succeed?
2. Was the `gateway_order_id` saved correctly?
3. Does the amount match?

### Issue: Still getting timeouts
**Possible causes:**
1. Server timeout settings too low
2. Database connection slow
3. Network issues between Payraizen and your server

**Check server timeout:**
```bash
# Check PHP max_execution_time
php -i | grep max_execution_time

# Check Apache/Nginx timeout settings
```

### Issue: Webhook not being called
**Solution:** 
1. Verify webhook URL with Payraizen support
2. Check server firewall allows incoming POST requests
3. Verify SSL certificate is valid

## Production Checklist

Before going live:
- [ ] Remove or secure the test endpoint (`/api/payment/payraizen/test-webhook`)
- [ ] Verify webhook URL with Payraizen: `https://ind6vendorfinal.zarwebcoders.in/api/payment/payraizen/webhook`
- [ ] Test with real payment (small amount)
- [ ] Monitor logs for 24 hours
- [ ] Set up alerts for failed webhooks
- [ ] Document the payment flow for your team

## Support

If issues persist:
1. Check the detailed documentation: `PAYRAIZEN_WEBHOOK_FIX.md`
2. Review logs for specific error messages
3. Contact Payraizen support with:
   - Your webhook URL
   - Sample webhook payload
   - Timestamp of failed attempts
   - Your merchant ID
