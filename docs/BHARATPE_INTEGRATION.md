# BharatPe Payment Gateway Integration Guide

## Overview
This guide explains how to set up and use automatic payment status tracking with BharatPe payment gateway.

## Merchant Information
- **Merchant ID**: 919241120006
- **Account Type**: Merchant Level View
- **Primary Contact**: Narendra

## Setup Instructions

### Step 1: Get API Credentials from BharatPe Dashboard

1. Log in to your BharatPe merchant dashboard: https://merchant.bharatpe.com/
2. Navigate to **Settings** → **API & Integrations**
3. Generate or copy your:
   - **API Key**
   - **API Secret**
4. Note down these credentials securely

### Step 2: Configure the Application

1. Open the file: `app/Config/BharatPe.php`
2. Update the following fields with your actual credentials:

```php
public $merchantId = '919241120006';  // Already set
public $apiKey = 'YOUR_ACTUAL_API_KEY';  // Replace this
public $apiSecret = 'YOUR_ACTUAL_API_SECRET';  // Replace this
```

3. Set the correct API base URL:
   - **Production**: `https://api.bharatpe.in/v1`
   - **Sandbox/Test**: `https://sandbox-api.bharatpe.in/v1`

### Step 3: Configure Webhook in BharatPe Dashboard

1. In your BharatPe dashboard, go to **Settings** → **Webhooks**
2. Add a new webhook with this URL:
   ```
   https://yourdomain.com/api/bharatpe/callback
   ```
   (Replace `yourdomain.com` with your actual domain)

3. For local testing, you can use tools like:
   - **ngrok**: `ngrok http 80`
   - **localtunnel**: `lt --port 80`

4. Enable webhook notifications for:
   - Payment Success
   - Payment Failed
   - Payment Pending

## Testing the Integration

### Local Testing

1. Open your browser and navigate to:
   ```
   http://localhost/Ind6TokenVendor/public/bharatpe_gateway_test.html
   ```

2. Enter test details:
   - **Vendor ID**: 1 (or any valid vendor ID)
   - **Amount**: 100 (or any amount in rupees)

3. Click **"Create BharatPe Payment"**

4. The system will:
   - Create a payment record in the database
   - Generate a QR code for payment
   - Provide a UPI intent link
   - Start automatic status checking every 5 seconds

5. Complete the payment using:
   - Scan the QR code with any UPI app
   - OR click "Open UPI App" button on mobile

6. Watch the status update automatically!

## API Endpoints

### 1. Create Payment
**Endpoint**: `POST /api/bharatpe/create`

**Request Body**:
```json
{
  "vendor_id": "1",
  "amount": 100
}
```

**Response**:
```json
{
  "success": true,
  "order_id": "BP_ABC123",
  "qr_code": "data:image/png;base64,...",
  "upi_intent": "upi://pay?...",
  "amount": 100
}
```

### 2. Check Payment Status
**Endpoint**: `POST /api/bharatpe/check-status`

**Request Body**:
```json
{
  "order_id": "BP_ABC123"
}
```

**Response (Success)**:
```json
{
  "status": "success",
  "message": "Payment successful",
  "utr": "123456789012"
}
```

**Response (Pending)**:
```json
{
  "status": "pending",
  "message": "Waiting for payment"
}
```

**Response (Failed)**:
```json
{
  "status": "error",
  "error": "Payment failed"
}
```

### 3. Webhook Callback
**Endpoint**: `POST /api/bharatpe/callback` (Automatic)

This endpoint receives automatic notifications from BharatPe when payment status changes.

**Webhook Payload**:
```json
{
  "order_id": "BP_ABC123",
  "status": "SUCCESS",
  "utr": "123456789012",
  "amount": 100,
  "timestamp": "2025-12-20T14:30:00Z"
}
```

## How Automatic Status Tracking Works

### Method 1: Polling (Client-Side)
The test page automatically checks payment status every 5 seconds:
1. User creates payment
2. JavaScript starts interval timer
3. Every 5 seconds, calls `/api/bharatpe/check-status`
4. Updates UI when status changes
5. Stops polling when payment succeeds or fails

### Method 2: Webhook (Server-Side)
BharatPe sends automatic notifications:
1. User completes payment in UPI app
2. BharatPe detects payment
3. BharatPe sends webhook to your server
4. Your server updates database automatically
5. Next status check returns updated status

## Database Schema

The payment is stored in the `payments` table with these fields:

| Field | Description |
|-------|-------------|
| `id` | Primary key |
| `vendor_id` | Vendor who receives payment |
| `amount` | Payment amount in rupees |
| `txn_id` | Your order ID (BP_XXX) |
| `gateway_order_id` | BharatPe's payment ID |
| `status` | pending/success/failed |
| `utr` | Bank UTR number (when successful) |
| `method` | bharatpe_qr |
| `gateway_name` | bharatpe |
| `verify_source` | bharatpe_api or bharatpe_webhook |
| `gateway_response` | Full API/webhook response (JSON) |
| `completed_time` | When payment completed |
| `created_at` | When payment initiated |

## Integration in Your App

### JavaScript Example

```javascript
// Create payment
async function initiatePayment(vendorId, amount) {
    const response = await fetch('http://yourserver.com/api/bharatpe/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ vendor_id: vendorId, amount: amount })
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Show QR code
        document.getElementById('qr').src = data.qr_code;
        
        // Or redirect to UPI app
        if (isMobile()) {
            window.location.href = data.upi_intent;
        }
        
        // Start checking status
        checkStatus(data.order_id);
    }
}

// Check status
async function checkStatus(orderId) {
    const response = await fetch('http://yourserver.com/api/bharatpe/check-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId })
    });
    
    const data = await response.json();
    
    if (data.status === 'success') {
        alert('Payment successful! UTR: ' + data.utr);
    } else if (data.status === 'pending') {
        // Check again after 5 seconds
        setTimeout(() => checkStatus(orderId), 5000);
    }
}
```

## Troubleshooting

### Issue: Payment created but status not updating

**Solution**:
1. Check if webhook is configured correctly in BharatPe dashboard
2. Verify webhook URL is accessible from internet
3. Check server logs: `writable/logs/log-YYYY-MM-DD.php`
4. Look for "BharatPe Callback:" entries

### Issue: QR code not displaying

**Solution**:
1. Verify API credentials are correct
2. Check if BharatPe API is responding
3. Check browser console for errors
4. Verify `callBharatPeAPI()` is returning data

### Issue: "Invalid callback" error

**Solution**:
1. Check webhook payload format
2. Verify `order_id` is present in callback
3. Check if payment exists in database

## Security Best Practices

1. **Never commit credentials**: Keep API keys in config file, add to `.gitignore`
2. **Validate webhooks**: Verify webhook signature (if BharatPe provides it)
3. **Use HTTPS**: Always use HTTPS in production
4. **Sanitize inputs**: Validate all user inputs
5. **Log everything**: Keep detailed logs for debugging

## Production Checklist

- [ ] API credentials configured correctly
- [ ] Webhook URL configured in BharatPe dashboard
- [ ] Webhook URL is HTTPS
- [ ] Test mode disabled (`$testMode = false`)
- [ ] Base URL set to production
- [ ] Error logging enabled
- [ ] Database backups configured
- [ ] Test with real small amount (₹1)

## Support

For BharatPe API issues:
- Email: support@bharatpe.com
- Phone: Check your merchant dashboard
- Documentation: https://developer.bharatpe.com/

For integration issues:
- Check logs in `writable/logs/`
- Review this documentation
- Test with the test page first

## Notes

- Minimum payment amount: ₹1
- Maximum payment amount: Check with BharatPe
- QR code validity: Usually 15-30 minutes
- Status check frequency: Recommended 5-10 seconds
- Webhook retry: BharatPe retries failed webhooks automatically
