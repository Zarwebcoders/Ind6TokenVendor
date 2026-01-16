<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kay2Pay Payment Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
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
            background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
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
            background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
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
            border-color: #0072ff;
            box-shadow: 0 0 0 3px rgba(0, 114, 255, 0.1);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
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
            box-shadow: 0 10px 20px rgba(0, 114, 255, 0.3);
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
            border-top: 3px solid #0072ff;
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
            border-left: 4px solid #0072ff;
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

        .pay-now-btn {
            display: none;
            background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
            color: white;
            border: none;
            padding: 18px 25px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
            text-decoration: none;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .pay-now-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>💎 Kay2Pay</h1>
            <p>Premium Payment Gateway</p>
            <span class="gateway-badge">Automatic QR Integration</span>
        </div>

        <div id="alertBox"></div>

        <form id="paymentForm">
            <div class="form-group">
                <label for="user_id">User ID</label>
                <input type="number" id="user_id" name="user_id" value="1" required>
            </div>

            <div class="form-group">
                <label for="customer_name">Customer Name *</label>
                <input type="text" id="customer_name" name="customer_name" placeholder="Enter full name"
                    value="Test User" required>
            </div>

            <div class="form-group">
                <label for="customer_email">Email Address *</label>
                <input type="email" id="customer_email" name="customer_email" placeholder="customer@example.com"
                    value="test@example.com" required>
            </div>

            <div class="form-group">
                <label for="customer_mobile">Mobile Number *</label>
                <input type="tel" id="customer_mobile" name="customer_mobile" placeholder="10-digit mobile number"
                    pattern="[0-9]{10}" maxlength="10" value="9876543210" required>
            </div>

            <div class="form-group">
                <label for="amount">Amount (INR) *</label>
                <input type="number" id="amount" name="amount" value="10.00" step="0.01" min="1" required>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                🚀 Pay Now
            </button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: #666;">Generating Payment Link...</p>
        </div>

        <div class="info-box">
            <h4>ℹ️ Gateway Information</h4>
            <ul>
                <li><strong>Provider:</strong> Kay2Pay</li>
                <li><strong>API:</strong> v1/payin/create_intent</li>
                <li><strong>Payment Type:</strong> UPI Intent / QR Code</li>
                <li><strong>Webhook:</strong> Configurable in dashboard</li>
            </ul>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="qr-modal" id="qrModal">
        <div class="qr-content">
            <h2 id="modalTitle">Scan & Pay</h2>
            <p id="modalDesc">Scan this QR code with any UPI app to complete payment</p>
            <img id="qrImage" src="" alt="QR Code">
            <a href="#" id="payNowBtn" class="pay-now-btn">📲 Open UPI App</a>
            <p style="margin-top: 15px;"><strong>Order ID:</strong> <span id="orderId"></span></p>
            <p><strong>Amount:</strong> ₹<span id="orderAmount"></span></p>
            <button class="close-btn" onclick="closeQRModal()">Cancel</button>
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

            // Show loading
            submitBtn.disabled = true;
            loading.style.display = 'block';
            alertBox.innerHTML = '';

            try {
                const response = await fetch(`${BASE_URL}api/kay2pay/initiate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user: userId,
                        amount: parseFloat(amount),
                        name: customerName,
                        email: customerEmail,
                        mobile: customerMobile
                    })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.payment_url) {
                        showQRCode(data.payment_url, data.order_id, amount);
                        showAlert('success', 'Payment initiated! Order ID: ' + data.order_id);
                        startPaymentPolling(data.order_id);
                    } else {
                        showAlert('success', 'Payment initiated successfully! Order ID: ' + data.order_id);
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
            const qrImage = document.getElementById('qrImage');
            const payNowBtn = document.getElementById('payNowBtn');
            const modalTitle = document.getElementById('modalTitle');
            const modalDesc = document.getElementById('modalDesc');

            qrImage.style.display = 'none';
            payNowBtn.style.display = 'none';

            if (qrString.startsWith('data:image') || qrString.startsWith('http')) {
                // If it's a URL or base64 image
                if (qrString.includes('upi://')) {
                    // UPI Link
                    payNowBtn.href = qrString;
                    payNowBtn.style.display = 'block';
                    modalTitle.textContent = "UPI Payment";
                    modalDesc.textContent = "Click below to pay via UPI app";
                } else {
                    qrImage.src = qrString;
                    qrImage.style.display = 'inline-block';
                }
            } else if (qrString.startsWith('upi://')) {
                payNowBtn.href = qrString;
                payNowBtn.style.display = 'block';
                modalTitle.textContent = "UPI Payment";
                modalDesc.textContent = "Click below to pay via UPI app";
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
            const maxPolls = 60; // 5 minutes

            const pollInterval = setInterval(async () => {
                pollCount++;

                try {
                    const response = await fetch(`${BASE_URL}api/kay2pay/check-status`, {
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

                        setTimeout(() => {
                            window.location.href = '<?= base_url('payment/success') ?>?txn=' + orderId;
                        }, 2000);
                    } else if (data.status === 'failed') {
                        clearInterval(pollInterval);
                        closeQRModal();
                        showAlert('error', '❌ Payment failed.');
                    }

                    if (pollCount >= maxPolls) {
                        clearInterval(pollInterval);
                    }

                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 5000);
        }

        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-error' : 'alert-info';
            alertBox.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        }
    </script>
</body>

</html>