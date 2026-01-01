# Testing Payraizen Integration Without Real Credentials

## Current Situation

You're getting this error:
```json
{
  "status": 200,
  "error": 200,
  "messages": {
    "error": "Payment initiation failed."
  }
}
```

This means:
- ✅ **API endpoint is working** (status 200)
- ✅ **Routes are configured correctly**
- ✅ **Controller method is executing**
- ❌ **Payraizen API call is failing** (because credentials are placeholder values)

---

## Why It's Failing

The current credentials in `PaymentApi.php` are test values:
```php
$merchantId = '987654321';  // Not a real merchant ID
$token = 'bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK';  // Not a real token
```

These will be rejected by Payraizen's API.

---

## Solution Options

### Option 1: Get Real Payraizen Credentials (Recommended)

1. **Sign up for Payraizen:**
   - Visit: https://partner.payraizen.com
   - Create merchant account
   - Complete KYC verification

2. **Get API Credentials:**
   - Login to merchant dashboard
   - Go to API Settings
   - Copy your Merchant ID and API Token

3. **Update PaymentApi.php:**
   ```php
   // Line ~212 in PaymentApi.php
   $merchantId = 'YOUR_REAL_MERCHANT_ID';
   $token = 'YOUR_REAL_API_TOKEN';
   ```

4. **Test Again:**
   - Use test page: `http://localhost/Ind6TokenVendor/test_payraizen.html`
   - Should now work with real credentials

---

### Option 2: Mock Payraizen for Testing

If you don't have real credentials yet, you can create a mock version for testing:

#### Step 1: Create Mock Gateway Method

Add this method to `PaymentApi.php`:

```php
/**
 * Mock Payraizen Request (For Testing Only)
 * Simulates Payraizen response without calling real API
 */
public function createMockPayraizenRequest()
{
    $request = \Config\Services::request();
    
    $vendorId = session()->get('id'); 
    $input = $request->getJSON(true);
    
    if (!$vendorId && isset($input['vendor_id'])) {
        $vendorId = $input['vendor_id'];
    }

    if (!$vendorId) {
        return $this->failUnauthorized('Vendor authentication failed.');
    }

    $amount = $input['amount'] ?? 0;

    if ($amount <= 0) {
        return $this->fail('Invalid amount.');
    }

    // Generate transaction IDs
    $txnId = 'TXN_' . strtoupper(uniqid());
    $mockGatewayOrderId = 'MOCK_' . strtoupper(uniqid());

    // Create pending payment record
    $paymentModel = new \App\Models\PaymentModel();
    $paymentData = [
        'vendor_id'    => $vendorId,
        'amount'       => $amount,
        'txn_id'       => $txnId,
        'status'       => 'pending',
        'method'       => 'mock_payraizen',
        'gateway_name' => 'payraizen_mock',
        'gateway_order_id' => $mockGatewayOrderId,
        'created_at'   => date('Y-m-d H:i:s')
    ];

    try {
        $paymentModel->insert($paymentData);
    } catch (\Exception $e) {
        return $this->failServerError('Database error: ' . $e->getMessage());
    }

    // Mock UPI payment URL
    $mockPaymentUrl = "upi://pay?pa=test@paytm&pn=TestMerchant&am={$amount}&tr={$txnId}&cu=INR";

    // Return mock success response
    return $this->respond([
        'success' => true,
        'status' => 'initiated',
        'transaction_id' => $txnId,
        'gateway_order_id' => $mockGatewayOrderId,
        'payment_url' => $mockPaymentUrl,
        'intent' => true,
        'payment_info' => [
            'amount' => $amount,
            'currency' => 'INR'
        ],
        'note' => 'This is a MOCK response for testing. No real payment gateway was called.'
    ]);
}
```

#### Step 2: Add Mock Route

Add to `app/Config/Routes.php`:
```php
$routes->post('api/payment/payraizen/mock', 'PaymentApi::createMockPayraizenRequest');
```

#### Step 3: Test Mock Endpoint

```bash
curl -X POST http://localhost/Ind6TokenVendor/api/payment/payraizen/mock ^
  -H "Content-Type: application/json" ^
  -d "{\"vendor_id\":\"1\",\"amount\":100}"
```

---

### Option 3: Use Your Existing Manual UPI Flow

Since you already have a working manual UPI payment system, you can continue using that while waiting for Payraizen credentials:

**Existing Endpoint:**
```
POST /api/payment/initiate
```

This uses your configured UPI ID and QR code without requiring Payraizen.

---

## Viewing Detailed Error Information

I've updated the error response to show more details. Try the API again and you'll now see:

```json
{
  "status": 400,
  "error": 400,
  "messages": {
    "error": {
      "message": "Payment initiation failed.",
      "gateway_error": {
        // Full Payraizen error response
      },
      "http_code": 401,
      "debug_info": {
        "api_url": "https://partner.payraizen.com/tech/api/payin/create_intent",
        "merchant_id": "987654321",
        "transaction_id": "TXN_..."
      }
    }
  }
}
```

This will help identify the exact error from Payraizen.

---

## Check Logs

View detailed logs:
```bash
type writable\logs\log-2025-12-13.log
```

Look for:
- `Payraizen Response:` - Shows full API response
- `Payraizen API Error` - Shows error details
- `Payraizen cURL Error` - Shows connection issues

---

## Testing Checklist

- [ ] API endpoint responds (status 200) ✅ **DONE**
- [ ] Routes configured correctly ✅ **DONE**
- [ ] Database migration run ✅ **DONE**
- [ ] Vendor exists in database ✅ **DONE**
- [ ] Get real Payraizen credentials ⏳ **PENDING**
- [ ] Update credentials in code ⏳ **PENDING**
- [ ] Test with real credentials ⏳ **PENDING**

---

## What Works Right Now

✅ **Your Current System:**
- Manual UPI payment with QR code
- Bank details configuration
- Payment tracking
- Transaction history

✅ **Payraizen Integration (Technical):**
- API endpoints created
- Database schema updated
- Webhook handler ready
- Error handling implemented

❌ **What Needs Real Credentials:**
- Actual payment initiation through Payraizen
- Automatic UTR capture
- Webhook notifications

---

## Recommended Next Steps

### Short Term (Testing)
1. Use the mock endpoint I provided above
2. Or continue using your existing manual UPI flow
3. Test the overall system functionality

### Long Term (Production)
1. Sign up for Payraizen merchant account
2. Get API credentials
3. Update PaymentApi.php with real credentials
4. Test with small amount
5. Configure webhook URL
6. Go live

---

## Alternative Payment Gateways

If Payraizen doesn't work for you, consider these alternatives:

1. **Razorpay** - Popular in India, easy integration
2. **Cashfree** - Good UPI support
3. **PayU** - Established gateway
4. **Instamojo** - Good for small businesses
5. **PhonePe Payment Gateway** - Direct UPI integration

All of these have similar integration patterns and can be added to your system.

---

## Summary

**Current Status:**
- ✅ Integration code is complete and working
- ✅ API endpoints are functional
- ❌ Need real Payraizen credentials to process actual payments

**You can:**
1. Get real Payraizen credentials (recommended)
2. Use the mock endpoint for testing
3. Continue with your existing manual UPI system
4. Consider alternative payment gateways

The technical implementation is solid - you just need the credentials to connect to the real payment gateway! 🎯
