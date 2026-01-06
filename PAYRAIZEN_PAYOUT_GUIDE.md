# Payraizen Payout Implementation Guide

## Overview

This document explains the complete Payraizen Payout (withdrawal) implementation for vendor withdrawals.

---

## 🎯 **What is Payout?**

**Payout** = Money going OUT from your business to vendors/users
- Vendors request withdrawal of their earnings
- You initiate payout via Payraizen API
- Payraizen processes the bank transfer
- Webhook notifies you when completed

---

## 📊 **Database Schema**

### **Payouts Table**

Run this SQL to create the table:

```sql
CREATE TABLE IF NOT EXISTS `payouts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `txn_id` varchar(100) NOT NULL,
  `gateway_order_id` varchar(100) DEFAULT NULL,
  `gateway_name` varchar(50) DEFAULT 'payraizen',
  `gateway_response` text DEFAULT NULL,
  `beneficiary_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `utr` varchar(100) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `method` varchar(50) DEFAULT 'payraizen_payout',
  `failure_reason` text DEFAULT NULL,
  `verify_source` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `txn_id` (`txn_id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `gateway_order_id` (`gateway_order_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🔌 **API Endpoints**

### **1. Initiate Payout**

**Endpoint:** `POST /api/payout/payraizen/initiate`

**Request:**
```json
{
  "vendor_id": 1,
  "amount": 1000,
  "beneficiary_name": "John Doe",
  "account_number": "1234567890",
  "ifsc_code": "HDFC0001234",
  "bank_name": "HDFC Bank"
}
```

**Success Response:**
```json
{
  "success": true,
  "status": "processing",
  "transaction_id": "PAYOUT_ABC123",
  "gateway_order_id": "PAYROPS123456",
  "message": "Payout initiated successfully",
  "payout_info": {
    "amount": 1000,
    "beneficiary_name": "John Doe",
    "account_number": "7890"
  }
}
```

**Error Response:**
```json
{
  "message": "Insufficient balance",
  "status": 400
}
```

---

### **2. Payout Webhook**

**Endpoint:** `POST /api/payout/payraizen/webhook`

**Webhook URL to provide to Payraizen:**
```
https://ind6vendorfinal.zarwebcoders.in/api/payout/payraizen/webhook
```

**Webhook Payload (from Payraizen):**
```json
{
  "status": "true",
  "msg": "Payout Webhook",
  "order_details": {
    "amount": 1000,
    "bank_utr": "987654321",
    "status": "success",
    "tid": "PAYROPS123456",
    "beneficiary_name": "John Doe",
    "account_number": "1234567890",
    "ifsc": "HDFC0001234"
  }
}
```

**Webhook Response:**
```json
{
  "status": "success",
  "message": "Payout status updated successfully",
  "transaction_id": "PAYOUT_ABC123"
}
```

---

## 🔄 **Payout Flow**

### **Complete Workflow:**

```
1. Vendor requests withdrawal
   ↓
2. Your system validates:
   - Sufficient balance?
   - Valid bank details?
   - Minimum withdrawal amount met?
   ↓
3. Call POST /api/payout/payraizen/initiate
   ↓
4. System creates payout record (status: pending)
   ↓
5. API calls Payraizen payout endpoint
   ↓
6. Payraizen responds with gateway_order_id
   ↓
7. System updates status to "processing"
   ↓
8. Payraizen processes bank transfer
   ↓
9. Payraizen sends webhook notification
   ↓
10. System updates status to "completed"
    ↓
11. Vendor receives money in bank account
```

---

## 💻 **Code Examples**

### **Example 1: Initiate Payout from Frontend**

```javascript
async function initiateWithdrawal() {
  const response = await fetch('/api/payout/payraizen/initiate', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      vendor_id: 1,
      amount: 1000,
      beneficiary_name: 'John Doe',
      account_number: '1234567890',
      ifsc_code: 'HDFC0001234',
      bank_name: 'HDFC Bank'
    })
  });

  const data = await response.json();
  
  if (data.success) {
    alert('Withdrawal initiated! Transaction ID: ' + data.transaction_id);
  } else {
    alert('Error: ' + data.message);
  }
}
```

### **Example 2: Check Payout Status**

```php
// In your controller
$payoutModel = new \App\Models\PayoutModel();
$payout = $payoutModel->getByTxnId('PAYOUT_ABC123');

if ($payout) {
    echo "Status: " . $payout['status'];
    echo "Amount: " . $payout['amount'];
    echo "UTR: " . $payout['utr'];
}
```

### **Example 3: Get Vendor's Payout History**

```php
$payoutModel = new \App\Models\PayoutModel();
$payouts = $payoutModel->getByVendor($vendorId, 10, 0);

foreach ($payouts as $payout) {
    echo "TXN: {$payout['txn_id']}, Amount: {$payout['amount']}, Status: {$payout['status']}\n";
}
```

---

## 🎨 **Status Flow**

| Status | Description | Next Action |
|--------|-------------|-------------|
| **pending** | Payout request created | Waiting for API call |
| **processing** | Sent to Payraizen | Waiting for bank transfer |
| **completed** | Money transferred | Notify vendor |
| **failed** | Transfer failed | Refund balance, notify vendor |

---

## 🔐 **Security Considerations**

### **1. Balance Validation**

Uncomment this code in `createPayraizenPayout()`:

```php
// Check if vendor has sufficient balance
if ($vendor['balance'] < $amount) {
    return $this->fail('Insufficient balance.');
}
```

### **2. Minimum Withdrawal Amount**

Add this validation:

```php
$minWithdrawal = 100; // Minimum ₹100
if ($amount < $minWithdrawal) {
    return $this->fail('Minimum withdrawal amount is ₹' . $minWithdrawal);
}
```

### **3. Daily Withdrawal Limit**

```php
$payoutModel = new \App\Models\PayoutModel();
$todayTotal = $payoutModel->selectSum('amount')
    ->where('vendor_id', $vendorId)
    ->where('DATE(created_at)', date('Y-m-d'))
    ->where('status !=', 'failed')
    ->first();

$dailyLimit = 50000; // ₹50,000 per day
if (($todayTotal['amount'] ?? 0) + $amount > $dailyLimit) {
    return $this->fail('Daily withdrawal limit exceeded');
}
```

### **4. Bank Account Verification**

Before allowing withdrawals, verify bank account:

```php
// Store verified bank accounts in a separate table
$bankAccountModel = new \App\Models\BankAccountModel();
$account = $bankAccountModel->where('vendor_id', $vendorId)
    ->where('account_number', $accountNumber)
    ->where('verified', 1)
    ->first();

if (!$account) {
    return $this->fail('Bank account not verified');
}
```

---

## 📝 **Configuration**

### **Payraizen Credentials**

Update these in `PaymentApi.php` or move to `.env`:

```php
// Current (hardcoded)
$merchantId = '987654321';
$token = 'bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK';

// Better (from .env)
$merchantId = getenv('PAYRAIZEN_MERCHANT_ID');
$token = getenv('PAYRAIZEN_API_TOKEN');
```

Add to `.env`:
```env
PAYRAIZEN_MERCHANT_ID=987654321
PAYRAIZEN_API_TOKEN=bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK
PAYRAIZEN_VERIFY_SSL=true
```

---

## 🧪 **Testing**

### **Test Payout Initiation**

```bash
curl -X POST https://ind6vendorfinal.zarwebcoders.in/api/payout/payraizen/initiate \
  -H "Content-Type: application/json" \
  -d '{
    "vendor_id": 1,
    "amount": 100,
    "beneficiary_name": "Test User",
    "account_number": "1234567890",
    "ifsc_code": "HDFC0001234",
    "bank_name": "HDFC Bank"
  }'
```

### **Test Payout Webhook**

```bash
curl -X POST https://ind6vendorfinal.zarwebcoders.in/api/payout/payraizen/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "status": "true",
    "msg": "Payout Webhook",
    "order_details": {
      "amount": 100,
      "bank_utr": "TEST987654321",
      "status": "success",
      "tid": "PAYROPS_TEST123",
      "beneficiary_name": "Test User"
    }
  }'
```

---

## 📊 **Monitoring & Logs**

### **Check Payout Logs**

```bash
tail -f writable/logs/log-$(date +%Y-%m-%d).log | grep -i "payout"
```

### **Expected Log Messages**

✅ **Success:**
```
Payraizen Payout Request - URL: ..., Data: {...}
Payraizen Payout Response: {"status":"true",...}
Payraizen Payout Webhook Processing: TID=..., Status=success, UTR=...
Payout updated successfully - TXN: PAYOUT_XXX, Status: completed
```

❌ **Errors:**
```
Payraizen Payout cURL Error: Connection timed out
Payout not found for gateway order ID: ...
Database update failed - ...
```

---

## 🚀 **Deployment Checklist**

Before going live:

- [ ] Run SQL migration to create `payouts` table
- [ ] Update Payraizen credentials in `.env`
- [ ] Configure webhook URL in Payraizen dashboard
- [ ] Test with small amount (₹10)
- [ ] Implement balance validation
- [ ] Set minimum/maximum withdrawal limits
- [ ] Add email notifications for payout status
- [ ] Set up monitoring/alerts for failed payouts
- [ ] Document payout process for support team

---

## 🆘 **Troubleshooting**

### **Issue: "Payout not found" in webhook**

**Solution:** The `gateway_order_id` wasn't saved. Check:
1. Did the initiation API succeed?
2. Was the response from Payraizen valid?
3. Check logs for database errors

### **Issue: Connection timeout**

**Solution:** Same as payin - server IP needs to be whitelisted by Payraizen.

### **Issue: Payout stuck in "processing"**

**Solution:**
1. Check if webhook is being received
2. Verify webhook URL in Payraizen dashboard
3. Contact Payraizen support with `gateway_order_id`

---

## 📞 **Support**

- **Payraizen Support:** Contact for API issues, webhook configuration
- **Your Server IP:** `66.29.148.120` (whitelist this)
- **Webhook URLs:**
  - Payin: `https://ind6vendorfinal.zarwebcoders.in/api/payment/payraizen/webhook`
  - Payout: `https://ind6vendorfinal.zarwebcoders.in/api/payout/payraizen/webhook`

---

## 🎉 **Summary**

You now have:
- ✅ Payout initiation API
- ✅ Payout webhook handler
- ✅ Database model with helper methods
- ✅ Comprehensive logging
- ✅ Error handling
- ✅ Fallback payment matching
- ✅ Routes configured

**Next:** Test with a small withdrawal and monitor the logs!
