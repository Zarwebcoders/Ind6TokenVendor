# Payraizen Payment Gateway Integration

## Overview
This document describes the Payraizen payment gateway integration for the Vendor Token system. Payraizen provides automated UPI payment processing with webhook support for real-time payment status updates.

## Features
- ✅ Automatic payment initiation through Payraizen API
- ✅ Deep link generation for UPI apps
- ✅ Webhook support for automatic payment verification
- ✅ UTR (Unique Transaction Reference) auto-capture
- ✅ Complete transaction logging
- ✅ Fallback to random user data if vendor details are incomplete

## Database Schema

### New Fields in `payments` Table
The following fields have been added to support gateway integration:

```sql
gateway_name VARCHAR(50) NULL           -- Gateway identifier (e.g., 'payraizen')
gateway_order_id VARCHAR(100) NULL      -- Gateway's transaction ID
gateway_response TEXT NULL              -- Full JSON response from gateway
```

## API Endpoints

### 1. Initiate Payraizen Payment

**Endpoint:** `POST /api/payment/payraizen/initiate`

**Headers:**
```json
{
  "Content-Type": "application/json"
}
```

**Request Body:**
```json
{
  "vendor_id": "123",
  "amount": 500
}
```

**Success Response (200):**
```json
{
  "success": true,
  "status": "initiated",
  "transaction_id": "TXN_ABC123",
  "gateway_order_id": "PR_XYZ789",
  "payment_url": "upi://pay?pa=merchant@upi&pn=Merchant&am=500...",
  "intent": true,
  "payment_info": {
    "amount": 500,
    "currency": "INR"
  }
}
```

**Error Response (400/500):**
```json
{
  "status": "error",
  "message": "Error description"
}
```

### 2. Payraizen Webhook Handler

**Endpoint:** `POST /api/payment/payraizen/webhook`

**Headers:**
```json
{
  "Content-Type": "application/json"
}
```

**Expected Webhook Payload from Payraizen:**
```json
{
  "order_details": {
    "tid": "PR_XYZ789",
    "status": "success",
    "bank_utr": "123456789012",
    "amount": 500,
    "timestamp": "2025-12-13T00:00:00Z"
  }
}
```

**Success Response (200):**
```json
{
  "status": "success",
  "message": "Payment status updated successfully",
  "transaction_id": "TXN_ABC123"
}
```

## Configuration

### Payraizen Credentials
Update the following in `app/Controllers/PaymentApi.php` (or move to `.env` for production):

```php
$merchantId = 'YOUR_MERCHANT_ID';
$token = 'YOUR_API_TOKEN';
```

### Recommended: Environment Variables
Add to `.env`:
```env
PAYRAIZEN_MERCHANT_ID=987654321
PAYRAIZEN_API_TOKEN=your_token_here
PAYRAIZEN_API_URL=https://partner.payraizen.com/tech/api/payin/create_intent
```

Then update the code to use:
```php
$merchantId = getenv('PAYRAIZEN_MERCHANT_ID');
$token = getenv('PAYRAIZEN_API_TOKEN');
```

## Webhook Setup

### Configure Webhook URL in Payraizen Dashboard
Set your webhook URL in the Payraizen merchant dashboard:
```
https://yourdomain.com/api/payment/payraizen/webhook
```

### Webhook Security (Recommended)
Add IP whitelisting or signature verification in production:

```php
public function handlePayraizenWebhook()
{
    // Verify webhook source
    $allowedIPs = ['123.45.67.89']; // Payraizen IPs
    $clientIP = $this->request->getIPAddress();
    
    if (!in_array($clientIP, $allowedIPs)) {
        return $this->failUnauthorized('Invalid webhook source');
    }
    
    // ... rest of webhook logic
}
```

## Payment Flow

### 1. Initiate Payment
```
Client App → POST /api/payment/payraizen/initiate
           ↓
System creates pending payment record
           ↓
System calls Payraizen API
           ↓
Payraizen returns deeplink
           ↓
Client receives payment URL
```

### 2. User Completes Payment
```
User clicks payment URL
           ↓
UPI app opens
           ↓
User completes payment
           ↓
Payraizen processes payment
```

### 3. Webhook Updates Status
```
Payraizen → POST /api/payment/payraizen/webhook
           ↓
System validates payload
           ↓
System updates payment status
           ↓
System saves UTR
           ↓
Payment marked as 'success'
```

## Database Migration

Run the migration to add gateway fields:

```bash
php spark migrate
```

Or manually run:
```sql
ALTER TABLE payments 
ADD COLUMN gateway_name VARCHAR(50) NULL AFTER method,
ADD COLUMN gateway_order_id VARCHAR(100) NULL AFTER gateway_name,
ADD COLUMN gateway_response TEXT NULL AFTER gateway_order_id;
```

## Testing

### Test Payment Initiation
```bash
curl -X POST http://localhost/api/payment/payraizen/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "vendor_id": "1",
    "amount": 100
  }'
```

### Test Webhook (Simulate Payraizen)
```bash
curl -X POST http://localhost/api/payment/payraizen/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "order_details": {
      "tid": "PR_TEST123",
      "status": "success",
      "bank_utr": "123456789012"
    }
  }'
```

## Error Handling

### Common Errors

1. **Vendor not found**
   - Ensure vendor_id exists in vendors table
   - Check vendor authentication

2. **Invalid amount**
   - Amount must be greater than 0
   - Amount should be numeric

3. **Payraizen API error**
   - Check merchant credentials
   - Verify API endpoint URL
   - Check network connectivity

4. **Webhook payload invalid**
   - Verify Payraizen webhook format
   - Check required fields (tid, bank_utr)

## Logging

All Payraizen interactions are logged in CodeIgniter logs:

```php
log_message('info', 'Payraizen Response: ' . $response);
log_message('info', 'Payraizen Webhook Received: ' . json_encode($payload));
log_message('error', 'Payraizen Webhook Error: ' . $error);
```

View logs at: `writable/logs/log-YYYY-MM-DD.log`

## Security Best Practices

1. **Use HTTPS** - Always use SSL/TLS for webhook endpoints
2. **Validate Webhook Source** - Implement IP whitelisting or signature verification
3. **Environment Variables** - Store credentials in `.env`, not in code
4. **Rate Limiting** - Implement rate limiting on API endpoints
5. **Input Validation** - Always validate and sanitize input data
6. **Error Messages** - Don't expose sensitive information in error messages

## Comparison: Payraizen vs Manual UPI

| Feature | Manual UPI | Payraizen Gateway |
|---------|-----------|-------------------|
| Payment Initiation | Manual QR/UPI ID | Automated deeplink |
| UTR Capture | Manual entry | Automatic via webhook |
| Payment Verification | Manual screenshot | Automatic |
| Success Rate | Depends on user | Higher (automated) |
| User Experience | Multiple steps | Single click |
| Admin Workload | High | Low |

## Support

For Payraizen API issues:
- Documentation: https://partner.payraizen.com/docs
- Support: support@payraizen.com

For integration issues:
- Check logs in `writable/logs/`
- Verify database schema
- Test with curl commands above
