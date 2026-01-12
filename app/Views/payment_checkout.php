<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Complete Payment | Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
        }

        .checkout-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .amount-display {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
        }

        .amount-display .currency {
            font-size: 24px;
            font-weight: bold;
        }

        .amount-display .amount {
            font-size: 48px;
            font-weight: bold;
            margin-left: 5px;
        }

        .qr-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }

        .qr-code {
            display: inline-block;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .qr-code img {
            width: 250px;
            height: 250px;
            display: block;
        }

        .timer-container {
            text-align: center;
            margin: 20px 0;
        }

        .timer {
            display: inline-block;
            padding: 12px 24px;
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 25px;
            color: #856404;
            font-weight: 600;
        }

        .timer i {
            margin-right: 8px;
        }

        .time-remaining {
            font-family: 'Courier New', monospace;
            font-size: 18px;
        }

        .upi-apps {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 25px 0;
            flex-wrap: wrap;
        }

        .upi-app {
            cursor: pointer;
            transition: transform 0.2s;
            padding: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .upi-app:hover {
            transform: scale(1.1);
        }

        .upi-app img {
            height: 40px;
            width: auto;
        }

        .payment-instructions {
            margin: 30px 0;
        }

        .instruction {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 12px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .step-number {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .instruction-text {
            color: #555;
            font-size: 14px;
        }

        .status-container {
            text-align: center;
            margin: 25px 0;
        }

        .status-message {
            padding: 15px;
            background: #e3f2fd;
            border-radius: 10px;
            color: #1976d2;
            font-weight: 500;
        }

        .status-message i {
            margin-right: 10px;
        }

        .order-details {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-size: 14px;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
            font-size: 14px;
            text-align: right;
        }

        /* Success Screen */
        .success-screen,
        .fail-screen {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .checkmark-wrapper {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            animation: scaleIn 0.5s ease-out;
        }

        .checkmark-wrapper i {
            font-size: 60px;
            color: #4caf50;
        }

        .fail-screen .checkmark-wrapper i {
            color: #f44336;
        }

        .success-message,
        .fail-message {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .success-subtext,
        .fail-subtext {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .countdown {
            font-size: 16px;
            opacity: 0.8;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .gift {
            position: absolute;
            font-size: 30px;
            animation: fall 4s linear;
            pointer-events: none;
        }

        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        @media (max-width: 600px) {
            .checkout-container {
                padding: 20px;
            }

            .amount-display .amount {
                font-size: 36px;
            }

            .qr-code img {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <!-- Main Checkout Container -->
    <div class="container qr-wrapper">
        <div class="checkout-container">
            <div class="header">
                <h1><i class="fas fa-shopping-cart"></i> Complete Payment</h1>
            </div>

            <div class="amount-display">
                <span class="currency">₹</span>
                <span class="amount"><?= $amount ?></span>
            </div>

            <!-- QR Code Section -->
            <?php if ($qr_code_url): ?>
                <div class="qr-container">
                    <h3 style="margin-bottom: 15px; color: #555;">Scan QR Code to Pay</h3>
                    <div class="qr-code">
                        <img src="<?= $qr_code_url ?>" alt="Scan this QR code to pay">
                    </div>
                </div>
            <?php else: ?>
                <div class="qr-container" style="padding: 40px 20px;">
                    <?php if ($gateway_name === 'vmpe'): ?>
                        <h3 style="margin-bottom: 15px; color: #555;">Link Ready</h3>
                        <p style="color: #666; margin-bottom: 20px;">Please use the "Pay with UPI App" button below to complete
                            your payment.</p>
                        <div style="font-size: 50px; color: #667eea; margin-bottom: 20px;">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    <?php else: ?>
                        <h3 style="margin-bottom: 15px; color: #d32f2f;">No QR Available</h3>
                        <p style="color: #666;">There was an issue generating the QR code. Please try again or use the link
                            below.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Timer -->
            <div class="timer-container">
                <div class="timer">
                    <i class="fas fa-clock"></i> Time remaining:
                    <span class="time-remaining" id="timer">5:00</span>
                </div>
            </div>

            <!-- UPI Apps (Mobile Only) -->
            <div class="upi-apps" id="upiApps" style="<?= $qr_code_url ? 'display: none;' : 'display: flex;' ?>">
                <div class="upi-app" onclick="openUpiApp()"
                    style="width: 100%; text-align: center; padding: 15px; background: #4caf50; color: white; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/UPI-Logo-vector.svg/200px-UPI-Logo-vector.svg.png"
                        alt="UPI" style="height: 25px; filter: brightness(0) invert(1);">
                    <span style="font-weight: bold; font-size: 16px;">PAY WITH UPI APP</span>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="payment-instructions">
                <div class="instruction">
                    <div class="step-number">1</div>
                    <div class="instruction-text">Open your UPI payment app</div>
                </div>
                <div class="instruction">
                    <div class="step-number">2</div>
                    <div class="instruction-text">Tap on 'Scan QR Code'</div>
                </div>
                <div class="instruction">
                    <div class="step-number">3</div>
                    <div class="instruction-text">Point camera at the QR code</div>
                </div>
                <div class="instruction">
                    <div class="step-number">4</div>
                    <div class="instruction-text">Confirm amount & pay</div>
                </div>
            </div>

            <!-- Status Message -->
            <div class="status-container">
                <div class="status-message">
                    <i class="fas fa-spinner fa-pulse"></i> <span id="statusText">Verifying payment...</span>
                </div>
            </div>

            <!-- Order Details -->
            <div class="order-details">
                <h3 style="margin-bottom: 15px; color: #333;"><i class="fas fa-receipt"></i> Order Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value"><?= $order_id ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value"><?= $created_at ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">UPI QR Payment</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">UPI ID</span>
                    <span class="detail-value"><?= $upi_id ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Screen -->
    <div class="success-screen" id="successScreen">
        <div class="checkmark-wrapper">
            <i class="fas fa-check"></i>
        </div>
        <div class="success-message">Payment Successful!</div>
        <div class="success-subtext">Thank you for your payment</div>
        <div class="countdown">Redirecting in <span id="successCountdown">3</span> seconds...</div>
    </div>

    <!-- Failure Screen -->
    <div class="fail-screen" id="failureScreen">
        <div class="checkmark-wrapper">
            <i class="fas fa-times"></i>
        </div>
        <div class="fail-message">Payment Failed</div>
        <div class="fail-subtext">Oops! Something went wrong</div>
        <div class="countdown">Redirecting in <span id="failCountdown">3</span> seconds...</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Configuration
        const transactionId = '<?= $transaction_id ?>';
        const checkStatusUrl = '<?= base_url('payment/check-status') ?>';
        const successUrl = '<?= base_url('payment/success?txn=') ?>' + transactionId;
        const failureUrl = '<?= base_url('payment/failure?txn=') ?>' + transactionId;
        const upiString = '<?= $upi_string ?>';

        // Detect mobile device
        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        // Show UPI app button on mobile OR if no QR is available
        if (isMobileDevice() || !<?= $qr_code_url ? 'true' : 'false' ?>) {
            document.getElementById('upiApps').style.display = 'flex';
        }

        // Open UPI app or Redirect
        function openUpiApp() {
            if (upiString && (upiString.startsWith('http') || upiString.startsWith('paytmmp') || upiString.startsWith('upi'))) {
                window.location.href = upiString;
            } else if (upiString) {
                // Fallback for any other custom protocol
                window.location.href = upiString;
            } else {
                alert('Payment link not available. Please refresh or contact support.');
            }
        }

        // Timer countdown (5 minutes)
        let timeLeft = 5 * 60; // 5 minutes in seconds
        const timerDisplay = document.getElementById('timer');

        const timerInterval = setInterval(function () {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;

            timerDisplay.textContent =
                (minutes < 10 ? '0' : '') + minutes + ':' +
                (seconds < 10 ? '0' : '') + seconds;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                clearInterval(statusCheckInterval);
                showAlert('error', failureUrl, 'Payment timeout! Please try again.');
            }

            timeLeft--;
        }, 1000);

        // Check payment status
        function checkPaymentStatus() {
            $.ajax({
                type: 'POST',
                url: checkStatusUrl,
                data: { transaction_id: transactionId },
                dataType: 'json',
                success: function (response) {
                    console.log('Status check:', response);

                    if (response.status === 'success') {
                        clearInterval(timerInterval);
                        clearInterval(statusCheckInterval);
                        showAlert('success', successUrl);
                    } else if (response.status === 'failed') {
                        clearInterval(timerInterval);
                        clearInterval(statusCheckInterval);
                        showAlert('error', failureUrl, response.message);
                    } else {
                        // Update status message
                        document.getElementById('statusText').textContent = response.message || 'Waiting for payment...';
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Status check error:', error);
                    // Continue checking even on error
                }
            });
        }

        // Start checking payment status every 3 seconds
        const statusCheckInterval = setInterval(checkPaymentStatus, 3000);

        // Initial check after 2 seconds
        setTimeout(checkPaymentStatus, 2000);

        // Show success/failure alert
        function showAlert(type, redirectUrl, message = '') {
            $('.qr-wrapper').hide();

            if (type === 'success') {
                const screen = document.getElementById('successScreen');
                screen.style.display = 'flex';

                // Add celebration effects
                for (let i = 0; i < 20; i++) {
                    const gift = document.createElement('div');
                    gift.classList.add('gift');
                    gift.innerText = ['🎁', '🎉', '✨', '🪙'][Math.floor(Math.random() * 4)];
                    gift.style.left = Math.random() * 100 + 'vw';
                    gift.style.animationDuration = (Math.random() * 2 + 3) + 's';
                    gift.style.fontSize = (Math.random() * 15 + 20) + 'px';
                    screen.appendChild(gift);
                    setTimeout(() => screen.removeChild(gift), 5000);
                }

                startCountdown('successCountdown', redirectUrl);
            } else {
                document.getElementById('failureScreen').style.display = 'flex';
                if (message) {
                    document.querySelector('.fail-subtext').textContent = message;
                }
                startCountdown('failCountdown', redirectUrl);
            }
        }

        // Countdown before redirect
        function startCountdown(elementId, redirectUrl) {
            let countdown = 3;
            const element = document.getElementById(elementId);

            const interval = setInterval(function () {
                countdown--;
                element.textContent = countdown;

                if (countdown === 0) {
                    clearInterval(interval);
                    window.location.href = redirectUrl;
                }
            }, 1000);
        }
    </script>
</body>

</html>