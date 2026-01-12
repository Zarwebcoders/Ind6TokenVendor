<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paytm Payment Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #667eea;
            font-size: 32px;
            font-weight: 700;
        }

        .logo p {
            color: #666;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        input,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }

        .info-box h4 {
            color: #333;
            margin-bottom: 10px;
        }

        .info-box ul {
            margin-left: 20px;
        }

        .info-box li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>💳 Paytm Payment</h1>
            <p>Test Payment Integration</p>
        </div>

        <div id="alertBox"></div>

        <form id="paymentForm">
            <div class="form-group">
                <label for="vendor_id">Vendor ID</label>
                <input type="number" id="vendor_id" name="vendor_id" value="1" required>
            </div>

            <div class="form-group">
                <label for="amount">Amount (INR)</label>
                <input type="number" id="amount" name="amount" value="100" step="0.01" min="1" required>
            </div>

            <div class="form-group">
                <label for="payment_type">Payment Type</label>
                <select id="payment_type" name="payment_type">
                    <option value="gateway">Paytm Gateway (Web)</option>
                    <option value="upi">Paytm UPI (Direct)</option>
                </select>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                Initiate Payment
            </button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: #666;">Processing...</p>
        </div>

        <div class="info-box">
            <h4>ℹ️ Test Information</h4>
            <ul>
                <li><strong>Gateway:</strong> Redirects to Paytm payment page</li>
                <li><strong>UPI:</strong> Shows QR code for direct payment</li>
                <li><strong>Callback:</strong> Automatic after payment</li>
                <li><strong>Success Page:</strong> /payment/paytm/success</li>
            </ul>
        </div>
    </div>

    <script>
        const form = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitBtn');
        const loading = document.getElementById('loading');
        const alertBox = document.getElementById('alertBox');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const vendorId = document.getElementById('vendor_id').value;
            const amount = document.getElementById('amount').value;
            const paymentType = document.getElementById('payment_type').value;

            // Show loading
            submitBtn.disabled = true;
            loading.style.display = 'block';
            alertBox.innerHTML = '';

            try {
                const endpoint = paymentType === 'upi'
                    ? '/api/paytm/upi/initiate'
                    : '/api/paytm/initiate';

                const response = await fetch('<?= base_url() ?>' + endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        vendor_id: vendorId,
                        amount: parseFloat(amount)
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (paymentType === 'upi') {
                        // Handle UPI payment
                        showAlert('success', 'UPI payment initiated! Order ID: ' + data.order_id);

                        // You can show QR code or redirect to a page with QR
                        console.log('UPI Intent:', data.upi_intent);
                        console.log('QR String:', data.qr_string);

                        // For mobile, you can redirect to UPI intent
                        if (confirm('Open UPI app to complete payment?')) {
                            window.location.href = data.upi_intent;
                        }
                    } else {
                        // Handle gateway payment - submit form to Paytm
                        showAlert('info', 'Redirecting to Paytm payment page...');

                        const paytmForm = document.createElement('form');
                        paytmForm.method = 'POST';
                        paytmForm.action = data.payment_url;

                        // Add all parameters
                        Object.keys(data.params).forEach(key => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = data.params[key];
                            paytmForm.appendChild(input);
                        });

                        document.body.appendChild(paytmForm);
                        paytmForm.submit();
                    }
                } else {
                    showAlert('error', data.message || 'Payment initiation failed');
                    submitBtn.disabled = false;
                    loading.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'An error occurred: ' + error.message);
                submitBtn.disabled = false;
                loading.style.display = 'none';
            }
        });

        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-error' : 'alert-info';

            alertBox.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        }
    </script>
</body>

</html>