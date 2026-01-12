# VMPE Payment Gateway Integration

## Overview
This document describes the integration of VMPE (KdsTechs) payment gateway into the Ind6TokenVendor platform.

## Gateway Information
- **Provider**: VMPE Fintech (KdsTechs)
- **API Endpoint**: `https://payments.vmpe.in/fintech/api/payin/create_intent`
- **Payment Methods**: UPI Intent, UPI Collect
- **Verification**: Automatic via webhook

## Files Created

### 1. View File
**Location**: `/app/Views/vmpe_test.php`

A beautiful, modern test page featuring:
- Gradient purple/blue design theme
- UPI Intent payment support
- QR Code modal for payment scanning
- Real-time payment status polling
- Responsive design with animations
- User-friendly form interface

### 2. Controller File
**Location**: `/app/Controllers/VmpeGatewayApi.php`

Main controller handling:
- Payment initiation
- Webhook processing
- Status checking
- Database integration

### 3. Routes Configuration
**Location**: `/app/Config/Routes.php`

Added routes:
```php
// VMPE Gateway Routes (KdsTechs Integration)
$routes->post('api/vmpe/initiate', 'VmpeGatewayApi::initiatePayment');
$routes->post('api/vmpe/webhook', 'VmpeGatewayApi::handleWebhook');
$routes->post('api/vmpe/check-status', 'VmpeGatewayApi::checkStatus');

// Test Page
$routes->get('payment/vmpe/test', function () {
    return view('vmpe_test');
});
```

## Configuration

### API Credentials
✅ **Credentials are now configured** in `/app/Controllers/VmpeGatewayApi.php`:

```php
private $apiUrl = 'https://payments.vmpe.in/fintech/api/payin/create_intent';
private $bearerToken = 'K6i4GOWCEvn69QZ8dCEgy9rRJFpw4yQD3WLnQRdb'; // Configured: 12-Jan-2026
private $clientId = '121';
private $clientSecret = 'AGeEUnn22TRCIXb1DSkAsW93xGUEkilysCjB0iJe';
```

**Status**: ✅ Ready to use!

### Webhook URL
The webhook callback URL for VMPE dashboard configuration:
```
https://yourdomain.com/api/vmpe/webhook
```

For local testing:
```
http://localhost:8888/Ind6TokenVendor/api/vmpe/webhook
```

## API Endpoints

### 1. Initiate Payment
**Endpoint**: `POST /api/vmpe/initiate`

**Request Body**:
```json
{
  "user": 1,
  "amount": 100,
  "payment_method": "upi_intent"
}
```

**Response (Success)**:
```json
{
  "success": true,
  "payment_url": "upi://pay?...",
  "intent": true,
  "paymentId": "VMPE1705065432123",
  "order_id": "VMPE1705065432123",
  "message": "Payment initiated successfully"
}
```

**Response (Error)**:
```json
{
  "success": false,
  "message": "Error description"
}
```

### 2. Webhook Handler
**Endpoint**: `POST /api/vmpe/webhook`

**Expected Payload from VMPE**:
```json
{
  "status": "success",
  "order_id": "VMPE1705065432123",
  "utr": "123456789012",
  "amount": 100,
  "txn_id": "VMPE_TXN_123"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Payment successful",
  "order_id": "VMPE1705065432123",
  "utr": "123456789012"
}
```

### 3. Check Payment Status
**Endpoint**: `POST /api/vmpe/check-status`

**Request Body**:
```json
{
  "order_id": "VMPE1705065432123"
}
```

**Response**:
```json
{
  "success": true,
  "status": "success",
  "payment_status": "success",
  "utr": "123456789012",
  "amount": 100,
  "message": "Payment completed successfully!"
}
```

## Payment Flow

### 1. User Initiates Payment
1. User fills form on test page
2. JavaScript sends POST request to `/api/vmpe/initiate`
3. Backend creates payment record in database
4. Backend calls VMPE API to create payment intent
5. VMPE returns QR code/UPI string

### 2. Payment Processing
1. QR code is displayed in modal
2. User scans QR with UPI app
3. User completes payment in UPI app
4. Frontend starts polling for status every 5 seconds

### 3. Payment Verification
1. VMPE sends webhook to `/api/vmpe/webhook`
2. Backend validates webhook data
3. Backend updates payment status in database
4. Status polling detects success
5. User is redirected to success page

## Database Schema

The payment is stored in the `payments` table with these fields:

```sql
- id (auto increment)
- txn_id (VMPE order ID)
- platform_txn_id (VMPE order ID)
- vendor_id (default: 1)
- user_id (from request)
- amount (payment amount)
- currency (INR)
- status (pending/success/failed)
- gateway_name (vmpe)
- method (upi)
- gateway_txn_id (UTR number)
- utr (UTR number)
- created_at
- updated_at
```

## Testing

### Access Test Page
Navigate to:
```
http://localhost:8888/Ind6TokenVendor/payment/vmpe/test
```

### Test Payment Flow
1. Enter User ID (e.g., 1)
2. Enter Amount (e.g., 100)
3. Select Payment Method (UPI Intent recommended)
4. Click "Initiate Payment"
5. QR code will appear in modal
6. Scan with UPI app to complete payment
7. Wait for automatic verification

### Monitor Logs
Check logs in `/writable/logs/` for:
- Payment initiation logs
- API request/response logs
- Webhook received logs
- Status update logs

## Features

### Frontend Features
- ✅ Modern gradient design
- ✅ Responsive layout
- ✅ QR code modal
- ✅ Real-time status polling
- ✅ Loading states
- ✅ Success/error alerts
- ✅ Smooth animations
- ✅ Mobile-friendly

### Backend Features
- ✅ Secure API integration
- ✅ Webhook verification
- ✅ Database logging
- ✅ Error handling
- ✅ Status tracking
- ✅ Auto-verification
- ✅ UTR capture

## Security Considerations

1. **Bearer Token**: Store in environment variables in production
2. **Client Secret**: Never expose in frontend code
3. **Webhook Validation**: Verify webhook source (add IP whitelist if needed)
4. **HTTPS**: Always use HTTPS in production
5. **Input Validation**: All inputs are validated before processing

## Production Checklist

- [ ] Update Bearer Token with production credentials
- [ ] Update API URL if different for production
- [ ] Configure webhook URL in VMPE dashboard
- [ ] Enable HTTPS
- [ ] Set up proper error logging
- [ ] Test webhook with VMPE support
- [ ] Add IP whitelist for webhook (if required)
- [ ] Remove test page or add authentication
- [ ] Set up monitoring for failed payments
- [ ] Configure email notifications for payment events

## Troubleshooting

### Payment Not Initiating
1. Check API credentials
2. Verify API endpoint is accessible
3. Check logs for cURL errors
4. Ensure SSL verification is properly configured

### Webhook Not Received
1. Verify webhook URL is publicly accessible
2. Check VMPE dashboard webhook configuration
3. Review server firewall settings
4. Check logs for incoming webhook requests

### Status Not Updating
1. Verify webhook is being received
2. Check database connection
3. Review payment status mapping
4. Check for errors in webhook handler

## Support

For VMPE API issues:
- Contact: VMPE Support
- Documentation: Check VMPE API docs
- Email: support@vmpe.in (example)

For Integration Issues:
- Check application logs
- Review this documentation
- Test with smaller amounts first

## Comparison with KdsTechs

This implementation follows the KdsTechs pattern you provided:
- Similar API structure
- Same webhook handling approach
- Identical order ID generation
- Matching user data preparation
- Consistent error handling

## Next Steps

1. **Get Production Credentials**: Contact VMPE for production API keys
2. **Test Thoroughly**: Test with various amounts and scenarios
3. **Monitor Performance**: Track success rates and response times
4. **Add Features**: Consider adding refund support, recurring payments, etc.
5. **Documentation**: Keep this document updated with any changes

---

**Created**: January 12, 2026
**Last Updated**: January 12, 2026
**Version**: 1.0.0
