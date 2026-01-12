# Paytm Success Response Handling - Documentation

## Overview
This document explains how to handle success responses from Paytm payment gateway in your CodeIgniter 4 application.

## Flow Diagram

```
User Initiates Payment
        ↓
POST /api/paytm/initiate
        ↓
Payment Record Created (status: pending)
        ↓
User Redirected to Paytm Payment Page
        ↓
User Completes Payment on Paytm
        ↓
Paytm Sends Callback (POST/GET)
        ↓
/api/paytm/callback receives response
        ↓
Checksum Verification
        ↓
Payment Status Updated in Database
        ↓
User Redirected to Success/Failure Page
```

## Key Components

### 1. PaytmGatewayApi Controller (`app/Controllers/PaytmGatewayApi.php`)

#### Methods:

**a) initiatePayment()**
- Creates a new payment record
- Generates Paytm parameters with checksum
- Returns payment URL and parameters
- **Endpoint**: `POST /api/paytm/initiate`

**b) callback()**
- Receives response from Paytm after payment
- Verifies checksum for security
- Updates payment status in database
- Redirects to appropriate page
- **Endpoint**: `POST/GET /api/paytm/callback`

**c) checkStatus()**
- Queries Paytm API for payment status
- Updates local database with latest status
- **Endpoint**: `POST /api/paytm/check-status`

### 2. PaymentCheckout Controller (`app/Controllers/PaymentCheckout.php`)

#### Methods:

**a) paytmSuccess()**
- Displays success page for Paytm payments
- Shows transaction details
- **Endpoint**: `GET /payment/paytm/success?order_id=XXX`

**b) success()**
- Generic success page for all payment gateways
- **Endpoint**: `GET /payment/success?txn=XXX`

**c) failure()**
- Displays failure page
- **Endpoint**: `GET /payment/failure?txn=XXX`

## Paytm Callback Response Structure

When Paytm sends a callback, it includes the following parameters:

```php
[
    'ORDERID' => 'PTM_ABC123',           // Your order ID
    'TXNID' => '20240107111212345678',   // Paytm transaction ID
    'BANKTXNID' => 'BANK123456',         // Bank transaction ID (UTR)
    'TXNAMOUNT' => '100.00',             // Transaction amount
    'STATUS' => 'TXN_SUCCESS',           // Transaction status
    'RESPMSG' => 'Txn Success',          // Response message
    'TXNDATE' => '2024-01-07 11:12:13',  // Transaction date
    'GATEWAYNAME' => 'HDFC',             // Gateway name
    'BANKNAME' => 'HDFC',                // Bank name
    'PAYMENTMODE' => 'UPI',              // Payment mode
    'CHECKSUMHASH' => 'abc123...'        // Checksum for verification
]
```

### Status Values:
- `TXN_SUCCESS` - Payment successful
- `TXN_FAILURE` - Payment failed
- `PENDING` - Payment pending

## Database Updates

When a successful payment is received, the following fields are updated:

```php
[
    'status' => 'success',
    'utr' => $bankTxnId,                    // Bank transaction ID
    'gateway_order_id' => $txnId,           // Paytm transaction ID
    'gateway_txn_id' => $bankTxnId,         // Bank transaction ID
    'completed_time' => date('Y-m-d H:i:s'),
    'verify_source' => 'paytm_callback',
    'gateway_response' => json_encode($paytmResponse),
    'updated_at' => date('Y-m-d H:i:s')
]
```

## Security - Checksum Verification

The callback handler verifies the checksum to ensure the response is authentic:

```php
$isValidChecksum = $this->verifyChecksum($paytmResponse, $checksumHash, $config->merchantKey);

if (!$isValidChecksum) {
    // Reject the callback
    log_message('error', 'Invalid checksum');
    return redirect()->to('payment/failure');
}
```

## Usage Example

### 1. Initiate Payment (Frontend)

```javascript
// Make API call to initiate payment
fetch('/api/paytm/initiate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        vendor_id: 123,
        amount: 100.00
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Create form and submit to Paytm
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = data.payment_url;
        
        // Add all parameters
        Object.keys(data.params).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = data.params[key];
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
});
```

### 2. Handle Callback (Automatic)

Paytm will automatically POST to `/api/paytm/callback` after payment.

The callback handler will:
1. Verify checksum
2. Update database
3. Redirect to success/failure page

### 3. Display Success Page

User will be redirected to:
```
/payment/paytm/success?order_id=PTM_ABC123
```

## Logging

All Paytm interactions are logged for debugging:

```php
log_message('info', 'Paytm Callback Received: ' . json_encode($paytmResponse));
log_message('info', 'Paytm: Checksum verified successfully');
log_message('info', 'Paytm: Payment marked as SUCCESS with UTR: ' . $utr);
```

Check logs at: `writable/logs/log-YYYY-MM-DD.log`

## Testing

### Test Credentials (Staging)
Configure in `app/Config/Paytm.php`:

```php
public $environment = 'staging';
public $merchantId = 'hhdaGX54213527799734';
public $merchantKey = '1OLuORB&@k13LBXv';
```

### Test Callback Manually

You can simulate a callback by sending a POST request to `/api/paytm/callback`:

```bash
curl -X POST http://localhost:8888/Ind6TokenVendor/api/paytm/callback \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "ORDERID=PTM_ABC123&STATUS=TXN_SUCCESS&TXNID=20240107111212345678&BANKTXNID=BANK123456&TXNAMOUNT=100.00&CHECKSUMHASH=..."
```

## Troubleshooting

### Issue: Callback not received
**Solution**: 
- Check if callback URL is accessible from internet
- Verify firewall settings
- Check Paytm merchant dashboard for callback URL configuration

### Issue: Invalid checksum error
**Solution**:
- Verify merchant key is correct
- Ensure all parameters are included in checksum calculation
- Check parameter order (should be sorted)

### Issue: Payment not updating
**Solution**:
- Check database connection
- Verify payment record exists with correct order_id
- Check logs for errors

## Configuration

### Callback URL Configuration

The callback URL is automatically set in the payment initiation:

```php
'CALLBACK_URL' => base_url('api/paytm/callback')
```

Make sure your `baseURL` is correctly configured in `app/Config/App.php`:

```php
public $baseURL = 'http://localhost:8888/Ind6TokenVendor/';
```

## Production Checklist

- [ ] Update Paytm environment to 'production'
- [ ] Update merchant credentials
- [ ] Configure production callback URL
- [ ] Enable SSL/HTTPS
- [ ] Test callback URL accessibility
- [ ] Remove test/debug routes
- [ ] Enable error logging
- [ ] Set up monitoring for failed payments

## API Endpoints Summary

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/paytm/initiate` | POST | Initiate payment |
| `/api/paytm/callback` | POST/GET | Receive Paytm callback |
| `/api/paytm/check-status` | POST | Check payment status |
| `/payment/paytm/success` | GET | Display success page |
| `/payment/failure` | GET | Display failure page |

## Support

For Paytm-specific issues:
- Paytm Developer Docs: https://developer.paytm.com/
- Paytm Support: https://paytm.com/care

For application issues:
- Check application logs
- Review database payment records
- Contact development team
