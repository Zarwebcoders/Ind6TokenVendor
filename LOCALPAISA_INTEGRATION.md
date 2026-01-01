# LocalPaisa Payment Gateway Integration

## Overview
LocalPaisa payment gateway has been successfully integrated into the Ind6TokenVendor platform alongside the existing PayRaizen gateway.

## Files Modified

### 1. Controller: `app/Controllers/PaymentTest.php`
Added three new methods:

#### `createLocalPaisaRequest()`
- **Purpose**: Initiates a LocalPaisa payment request
- **Endpoint**: `POST /api/payment/localpaisa/initiate`
- **Parameters**:
  - `amount` (required): Payment amount
  - `buyer_name` (optional): Customer name
  - `buyer_email` (optional): Customer email
  - `buyer_phone` (optional): Customer mobile number
  - `vendor_id` (required): Vendor ID

- **Response**:
  ```json
  {
    "success": true,
    "message": "Payment initiated successfully",
    "payment_url": "upi://pay?...",
    "intent": true,
    "payment_id": "LP_ABC123",
    "transaction_id": "LP_ABC123"
  }
  ```

#### `handleLocalPaisaWebhook()`
- **Purpose**: Processes webhook callbacks from LocalPaisa
- **Endpoint**: `POST /payment/localpaisa/webhook`
- **Handles Two Event Types**:
  1. **Payout** - For payout transactions
  2. **Upi_Transaction** - For UPI payment confirmations

- **Webhook Payload Example**:
  ```json
  {
    "events": "Upi_Transaction",
    "dataContent": {
      "status": "SUCCESS",
      "order_Id": "LP_ABC123",
      "Transaction_Id": "TXN123456789"
    }
  }
  ```

#### `handlePaymentSuccess()`
- **Purpose**: Updates payment status to completed
- **Private method** called by webhook handler
- Updates database with transaction ID and completion timestamp

### 2. Routes: `app/Config/Routes.php`
Added two new routes:
```php
$routes->post('api/payment/localpaisa/initiate', 'PaymentTest::createLocalPaisaRequest');
$routes->post('payment/localpaisa/webhook', 'PaymentTest::handleLocalPaisaWebhook');
```

### 3. Test Page: `app/Views/payment_test.php`
- Added gateway selector dropdown
- Dynamic API endpoint routing based on selected gateway
- Supports both PayRaizen and LocalPaisa

## API Configuration

### LocalPaisa Credentials
```php
$authorizedKey = 't15wECFgWNQoe+8cwkT/awPk+miH7e28fZaU51PjcXM0yzdT5PTFdw==';
```

### LocalPaisa API Endpoint
```
POST http://api.localpaisa.com/api/Payment
```

### Headers Required
```
AuthorizedKey: t15wECFgWNQoe+8cwkT/awPk+miH7e28fZaU51PjcXM0yzdT5PTFdw==
Content-Type: application/json
```

## How to Test

### 1. Access Test Page
Navigate to: `http://localhost/Ind6TokenVendor/payment/test`

### 2. Fill in Test Details
- **Amount**: Enter test amount (e.g., 100)
- **Buyer Name**: Enter customer name
- **Buyer Email**: Enter email (optional)
- **Buyer Phone**: Enter 10-digit mobile number
- **Vendor ID**: Select vendor (default: 1)
- **Payment Gateway**: Select "LocalPaisa"

### 3. Create Payment
Click "Create Test Payment" button

### 4. Complete Payment
- On mobile: UPI intent link will open payment app automatically
- On desktop: Click "Pay Now" button to open payment link

### 5. Webhook Callback
LocalPaisa will send webhook to:
```
POST http://yourdomain.com/Ind6TokenVendor/payment/localpaisa/webhook
```

## Database Schema

Payment records are stored in the `payments` table with:
- `platform_txn_id`: Order ID (e.g., LP_ABC123)
- `gateway_txn_id`: LocalPaisa transaction ID
- `status`: pending → completed/failed
- `payment_method`: 'localpaisa'
- `gateway_name`: 'localpaisa'
- `gateway_response`: Full API response (JSON)
- `completed_at`: Completion timestamp

## Features Implemented

✅ **Payment Initiation** - Create LocalPaisa payment requests  
✅ **Webhook Handling** - Process payment confirmations  
✅ **Database Integration** - Store and update payment records  
✅ **Error Handling** - Comprehensive error logging and handling  
✅ **Random Data Generation** - Auto-generate email/name if missing  
✅ **UPI Intent Support** - Direct UPI app integration  
✅ **Logging** - All requests/responses logged for debugging  
✅ **Test Page** - User-friendly test interface  

## Payment Flow

```
1. User submits payment form
   ↓
2. createLocalPaisaRequest() called
   ↓
3. Payment record created (status: pending)
   ↓
4. API request sent to LocalPaisa
   ↓
5. LocalPaisa returns intent/payment link
   ↓
6. User completes payment in UPI app
   ↓
7. LocalPaisa sends webhook callback
   ↓
8. handleLocalPaisaWebhook() processes callback
   ↓
9. handlePaymentSuccess() updates status
   ↓
10. Payment marked as completed
```

## Error Handling

### API Errors
- cURL errors are caught and logged
- Payment status updated to 'failed'
- User-friendly error messages returned

### Webhook Errors
- Invalid payloads rejected
- Non-SUCCESS statuses logged
- Missing order IDs handled gracefully

### Logging
All operations are logged using CodeIgniter's `log_message()`:
- `info`: Successful operations
- `error`: Failures and exceptions

## Security Considerations

⚠️ **Important**: 
- Move `$authorizedKey` to environment variables (.env file)
- Implement webhook signature verification
- Add IP whitelisting for webhook endpoint
- Use HTTPS in production
- Sanitize all user inputs

## Next Steps

1. **Move API Key to .env**
   ```env
   LOCALPAISA_AUTHORIZED_KEY=t15wECFgWNQoe+8cwkT/awPk+miH7e28fZaU51PjcXM0yzdT5PTFdw==
   ```

2. **Add Webhook Signature Verification**
   Verify webhook authenticity using signature/hash

3. **Production Deployment**
   - Update webhook URL to production domain
   - Enable HTTPS
   - Test with real transactions

4. **Monitoring**
   - Set up alerts for failed payments
   - Monitor webhook delivery
   - Track payment success rates

## Support

For LocalPaisa API documentation and support:
- API Endpoint: http://api.localpaisa.com
- Contact LocalPaisa support for API documentation

---

**Integration Date**: December 31, 2025  
**Version**: 1.0  
**Status**: ✅ Ready for Testing
