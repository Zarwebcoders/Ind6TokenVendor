<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VMPE Payment Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
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
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            background: linear-gradient(135deg, #1e3c72 0%, #7e22ce 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 32px;
            font-weight: 700;
        }

        .logo p {
            color: #666;
            margin-top: 5px;
            font-size: 14px;
        }

        .gateway-badge {
            background: linear-gradient(135deg, #1e3c72 0%, #7e22ce 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
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
            border-color: #7e22ce;
            box-shadow: 0 0 0 3px rgba(126, 34, 206, 0.1);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #1e3c72 0%, #7e22ce 100%);
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
            box-shadow: 0 10px 20px rgba(126, 34, 206, 0.3);
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
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
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
            border-top: 3px solid #7e22ce;
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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
            border-left: 4px solid #7e22ce;
        }

        .info-box h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .info-box ul {
            margin-left: 20px;
        }

        .info-box li {
            margin-bottom: 5px;
        }

        .feature-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .feature-item {
            background: white;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feature-icon {
            font-size: 18px;
        }

        .qr-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .qr-modal.active {
            display: flex;
        }

        .qr-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            max-width: 400px;
            animation: scaleIn 0.3s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .qr-content img {
            max-width: 300px;
            margin: 20px 0;
            border-radius: 10px;
        }

        .close-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>💳 VMPE Payment</h1>
            <p>Fintech Payment Gateway</p>
            <span class="gateway-badge">KdsTechs Integration</span>
        </div>

        <div id="alertBox"></div>

        <form id="paymentForm">
            <div class="form-group">
                <label for="user_id">User ID</label>
                <input type="number" id="user_id" name="user_id" value="1" required>
            </div>

            <div class="form-group">
                <label for="customer_name">Customer Name *</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Enter full name" required>
            </div>

            <div class="form-group">
                <label for="customer_email">Email Address *</label>
                <input type="email" id="customer_email" name="customer_email" placeholder="customer@example.com"
                    required>
            </div>

            <div class="form-group">
                <label for="customer_mobile">Mobile Number *</label>
                <input type="tel" id="customer_mobile" name="customer_mobile" placeholder="10-digit mobile number"
                    pattern="[0-9]{10}" maxlength="10" required>
            </div>

            <div class="form-group">
                <label for="amount">Amount (INR) *</label>
                <input type="number" id="amount" name="amount" value="100" step="0.01" min="1" required>
            </div>

            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method">
                    <option value="upi_intent">UPI Intent (QR Code)</option>
                    <option value="upi_collect">UPI Collect</option>
                </select>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                🚀 Initiate Payment
            </button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: #666;">Processing your request...</p>
        </div>

        <div class="info-box">
            <h4>ℹ️ Gateway Information</h4>
            <ul>
                <li><strong>Provider:</strong> VMPE Fintech (KdsTechs)</li>
                <li><strong>API Endpoint:</strong> payments.vmpe.in</li>
                <li><strong>Payment Type:</strong> UPI Intent & Collect</li>
                <li><strong>Webhook:</strong> Automatic verification</li>
            </ul>

            <div class="feature-list">
                <div class="feature-item">
                    <span class="feature-icon">⚡</span>
                    <span>Instant</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🔒</span>
                    <span>Secure</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">✅</span>
                    <span>Auto-Verify</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">📱</span>
                    <span>UPI Ready</span>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="qr-modal" id="qrModal">
        <div class="qr-content">
            <h2>Scan QR Code</h2>
            <p>Scan this QR code with any UPI app to complete payment</p>
            <img id="qrImage" src="" alt="QR Code">
            <p><strong>Order ID:</strong> <span id="orderId"></span></p>
            <p><strong>Amount:</strong> ₹<span id="orderAmount"></span></p>
            <button class="close-btn" onclick="closeQRModal()">Close</button>
        </div>
    </div>

    <script>
        const BASE_URL = '<?= base_url() ?>'.replace(/\/$/, '') + '/index.php/';
        const form = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitBtn');
        const loading = document.getElementById('loading');
        const alertBox = document.getElementById('alertBox');
        const qrModal = document.getElementById('qrModal');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const userId = document.getElementById('user_id').value;
            const amount = document.getElementById('amount').value;
            const customerName = document.getElementById('customer_name').value;
            const customerEmail = document.getElementById('customer_email').value;
            const customerMobile = document.getElementById('customer_mobile').value;
            const paymentMethod = document.getElementById('payment_method').value;

            // Show loading
            submitBtn.disabled = true;
            loading.style.display = 'block';
            alertBox.innerHTML = '';

            try {
                const response = await fetch(`${BASE_URL}api/vmpe/initiate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user: userId,
                        amount: parseFloat(amount),
                        name: customerName,
                        email: customerEmail,
                        mobile: customerMobile,
                        payment_method: paymentMethod
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.intent && data.payment_url) {
                        // Show QR Code
                        showQRCode(data.payment_url, data.paymentId, amount);
                        showAlert('success', 'Payment initiated! Order ID: ' + data.paymentId);

                        // Start polling for payment status
                        startPaymentPolling(data.paymentId);
                    } else {
                        showAlert('success', 'Payment initiated successfully! Order ID: ' + data.paymentId);
                    }
                } else {
                    showAlert('error', data.message || 'Payment initiation failed');
                }

                submitBtn.disabled = false;
                loading.style.display = 'none';

            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'An error occurred: ' + error.message);
                submitBtn.disabled = false;
                loading.style.display = 'none';
            }
        });

        function showQRCode(qrString, orderId, amount) {
            // ONLY show QR if it's already an image (Base64 or a direct URL provided by VMPE)
            if (qrString.startsWith('data:image') || (qrString.startsWith('http') && (qrString.includes('.png') || qrString.includes('.jpg') || qrString.includes('.svg') || qrString.includes('qr')))) {
                document.getElementById('qrImage').src = qrString;
                document.getElementById('qrImage').style.display = 'inline-block';
            } else {
                // DO NOT generate QR code locally for VMPE gateway
                document.getElementById('qrImage').style.display = 'none';
                console.info('VMPE: Local QR generation disabled for string:', qrString);
            }

            document.getElementById('orderId').textContent = orderId;
            document.getElementById('orderAmount').textContent = amount;
            qrModal.classList.add('active');
        }

        function closeQRModal() {
            qrModal.classList.remove('active');
        }

        function startPaymentPolling(orderId) {
            let pollCount = 0;
            const maxPolls = 60; // Poll for 5 minutes (60 * 5 seconds)

            const pollInterval = setInterval(async () => {
                pollCount++;

                try {
                    const response = await fetch(`${BASE_URL}api/vmpe/check-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            order_id: orderId
                        })
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        clearInterval(pollInterval);
                        closeQRModal();
                        showAlert('success', '✅ Payment successful! UTR: ' + data.utr);

                        // Redirect to success page after 2 seconds
                        setTimeout(() => {
                            window.location.href = 'http://localhost:8888/Ind6TokenVendor/index.php/payment/success?txn=' + orderId;
                        }, 2000);
                    } else if (data.status === 'failed') {
                        clearInterval(pollInterval);
                        closeQRModal();
                        showAlert('error', '❌ Payment failed. Please try again.');
                    }

                    // Stop polling after max attempts
                    if (pollCount >= maxPolls) {
                        clearInterval(pollInterval);
                        showAlert('info', 'Payment verification timeout. Please check your payment status.');
                    }

                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 5000); // Poll every 5 seconds
        }

        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-error' : 'alert-info';

            alertBox.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        }
    </script>
</body>

</html>