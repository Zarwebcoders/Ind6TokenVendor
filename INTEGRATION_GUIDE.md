# Payment Gateway Integration Guide

This guide explains how to integrate the Ind6Token Vendor Payment Gateway into any web or mobile application (Android/iOS with WebView or JS support).

## Prerequisites

1.  **Vendor Account**: You must have a vendor account on the Ind6Token Vendor Dashboard.
2.  **Bank Details**: Ensure your UPI QR Code and Bank Details are saved in the **Utilities** section of the dashboard.
3.  **Vendor ID**: Obtain your unique `Vendor ID` from the Dashboard Home > Payment Gateway Integration section.

---

## Integration Steps

### Step 1: Add the JavaScript SDK

Add the following JavaScript class to your application's code. This handles communication with the payment server.

```javascript
class PaymentService {
    constructor(baseUrl, vendorId) {
        this.baseUrl = baseUrl; // e.g., 'https://your-domain.com/api/payment'
        this.vendorId = vendorId;
    }

    // 1. Initiate Payment
    async initiate(amount) {
        try {
            const response = await fetch(`${this.baseUrl}/initiate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    vendor_id: this.vendorId, 
                    amount: amount 
                })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
            return result; 
        } catch (error) {
            console.error('Initiation Error:', error);
            throw error;
        }
    }

    // 2. Update Status (After User Pays)
    async updateStatus(txnId, utr) {
        try {
            const response = await fetch(`${this.baseUrl}/update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    transaction_id: txnId, 
                    status: 'success', 
                    utr: utr 
                })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
            return result;
        } catch (error) {
            console.error('Update Error:', error);
            throw error;
        }
    }
}
```

### Step 2: Initialize the Service

Initialize the service with your Base URL and Vendor ID (found on your dashboard).

```javascript
/* REPLACE WITH YOUR ACTUAL VALUES FROM DASHBOARD */
const BASE_URL = 'http://localhost/Ind6TokenVendor/api/payment'; 
const VENDOR_ID = '1'; 

const paymentApi = new PaymentService(BASE_URL, VENDOR_ID);
```

### Step 3: Initiate a Transaction

When the user clicks "Pay", call the `initiate` method. This will return the Transaction ID and the QR Code URL.

```javascript
async function startPaymentFlow(amount) {
    try {
        console.log("Creating transaction...");
        const data = await paymentApi.initiate(amount);
        
        // 1. Store Transaction ID
        const currentTxnId = data.transaction_id;
        
        // 2. Display QR Code to User
        const qrUrl = data.bank_details.upi_qr; // URL to QR Image
        // Example: document.getElementById('qr-image').src = qrUrl;
        console.log("Scan this QR:", qrUrl);
        
        return currentTxnId;
    } catch (err) {
        alert("Failed to start payment: " + err.message);
    }
}
```

### Step 4: Confirm Payment (UTR Update)

After the user pays via their UPI app (GPay, PhonePe, Paytm), they will receive a **UTR** (Reference Number). You must capture this UTR (via an input field or auto-detection if built into a native app) and send it to the server.

```javascript
async function confirmPayment(txnId, userProvidedUtr) {
    try {
        const result = await paymentApi.updateStatus(txnId, userProvidedUtr);
        
        if (result.status === 'success') {
            alert("Payment Verified Successfully!");
            // Redirect or Unlock content
        }
    } catch (err) {
        alert("Verification failed: " + err.message);
    }
}
```

---

## API Reference

### 1. Endpoint: `/initiate` (POST)
**Request:**
```json
{
    "vendor_id": "1",
    "amount": 500
}
```
**Response:**
```json
{
    "status": "initiated",
    "transaction_id": "TXN_6578A...",
    "bank_details": {
        "upi_id": "vendor@upi",
        "upi_qr": "http://.../qr.png",
        ...
    }
}
```

### 2. Endpoint: `/update` (POST)
**Request:**
```json
{
    "transaction_id": "TXN_6578A...",
    "status": "success",
    "utr": "312005489652"
}
```
**Response:**
```json
{
    "status": "success",
    "message": "Transaction status and UTR updated successfully."
}
```
