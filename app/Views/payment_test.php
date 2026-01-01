<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Test Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .test-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
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
            transition: transform 0.2s;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .info-box p {
            color: #1976d2;
            font-size: 13px;
            margin: 5px 0;
        }

        .test-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed #e0e0e0;
        }

        .test-btn {
            padding: 10px;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .test-btn:hover {
            background: #667eea;
            color: white;
        }

        .test-btn.success {
            border-color: #4caf50;
            color: #4caf50;
        }

        .test-btn.success:hover {
            background: #4caf50;
            color: white;
        }

        .test-btn.danger {
            border-color: #f44336;
            color: #f44336;
        }

        .test-btn.danger:hover {
            background: #f44336;
            color: white;
        }
    </style>
</head>

<body>
    <div class="test-container">
        <h1><i class="fas fa-flask"></i> Payment Test</h1>
        <p class="subtitle">Test the payment checkout system</p>

        <div class="alert" id="alertBox"></div>

        <div class="info-box">
            <p><i class="fas fa-info-circle"></i> <strong>Test Mode</strong></p>
            <p>This page creates test payments. Use the simulation buttons below to test success/failure.</p>
        </div>

        <form id="paymentForm">
            <div class="form-group">
                <label for="amount">Amount (₹)</label>
                <input type="number" id="amount" name="amount" value="100" min="1" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="buyer_name">Buyer Name</label>
                <input type="text" id="buyer_name" name="buyer_name" value="Test User" required>
            </div>

            <div class="form-group">
                <label for="buyer_email">Buyer Email</label>
                <input type="email" id="buyer_email" name="buyer_email" value="test@example.com">
            </div>

            <div class="form-group">
                <label for="buyer_phone">Buyer Phone</label>
                <input type="tel" id="buyer_phone" name="buyer_phone" value="9876543210" pattern="[0-9]{10}">
            </div>

            <div class="form-group">
                <label for="vendor_id">Vendor ID</label>
                <input type="number" id="vendor_id" name="vendor_id" value="1" required>
            </div>

            <div class="form-group">
                <label for="gateway">Payment Gateway</label>
                <select id="gateway" name="gateway" required>
                    <option value="payraizen">PayRaizen</option>
                    <option value="localpaisa">LocalPaisa</option>
                </select>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                <i class="fas fa-rocket"></i> Create Test Payment
            </button>
        </form>

        <div class="test-buttons" id="testButtons" style="display: none;">
            <button class="test-btn success" onclick="simulateSuccess()">
                <i class="fas fa-check"></i> Simulate Success
            </button>
            <button class="test-btn danger" onclick="simulateFailure()">
                <i class="fas fa-times"></i> Simulate Failure
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentTransactionId = null;

        // Construct base URL from current location
        const baseUrl = window.location.origin + '/Ind6TokenVendor/';

        console.log('Current URL:', window.location.href); // Debug
        console.log('Origin:', window.location.origin); // Debug
        console.log('Base URL:', baseUrl); // Debug

        // Handle form submission
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();

            const gateway = $('#gateway').val();
            const formData = {
                amount: $('#amount').val(),
                buyer_name: $('#buyer_name').val(),
                buyer_email: $('#buyer_email').val(),
                buyer_phone: $('#buyer_phone').val(),
                vendor_id: $('#vendor_id').val()
            };

            // Determine API endpoint based on gateway
            let apiEndpoint;
            if (gateway === 'localpaisa') {
                apiEndpoint = baseUrl + 'api/payment/localpaisa/initiate';
            } else {
                apiEndpoint = baseUrl + 'api/payment/test/create';
            }

            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

            $.ajax({
                url: apiEndpoint,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response); // Debug
                    
                    if (response.success) {
                        currentTransactionId = response.transaction_id || response.payment_id;
                        
                        // Check if we have a payment URL
                        if (response.payment_url) {
                            const gatewayName = gateway === 'localpaisa' ? 'LocalPaisa' : 'PayRaizen';
                            showAlert('success', `${gatewayName} payment initiated! Opening payment app...`);
                            
                            // Show Pay Now button
                            $('#testButtons').html(`
                                <button class="btn" onclick="window.location.href='${response.payment_url}'" style="background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);">
                                    <i class="fas fa-mobile-alt"></i> Pay Now (₹${$('#amount').val()})
                                </button>
                            `).slideDown();
                            
                            // Open payment URL automatically on mobile
                            if (/Android|webOS|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                                setTimeout(function() {
                                    window.location.href = response.payment_url;
                                }, 1000);
                            }
                            
                            // For LocalPaisa, don't redirect to checkout immediately
                            if (gateway !== 'localpaisa') {
                                // Redirect to checkout after 3 seconds for PayRaizen
                                const checkoutUrl = baseUrl + 'payment/checkout?txn_id=' + currentTransactionId;
                                console.log('Will redirect to:', checkoutUrl); // Debug
                                
                                setTimeout(function() {
                                    window.location.href = checkoutUrl;
                                }, 3000);
                            }
                        } else {
                            // Fallback for payments without payment URL
                            showAlert('success', 'Payment created! Redirecting to checkout...');
                            
                            // Show test buttons
                            $('#testButtons').slideDown();

                            // Redirect to checkout after 2 seconds
                            const checkoutUrl = baseUrl + 'payment/checkout?txn_id=' + currentTransactionId;
                            console.log('Redirecting to:', checkoutUrl); // Debug
                            
                            setTimeout(function() {
                                window.location.href = checkoutUrl;
                            }, 2000);
                        }
                    } else {
                        showAlert('error', response.message || 'Failed to create payment');
                        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-rocket"></i> Create Test Payment');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    showAlert('error', 'An error occurred. Please try again.');
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-rocket"></i> Create Test Payment');
                }
            });
        });

        function showAlert(type, message) {
            const alertBox = $('#alertBox');
            alertBox.removeClass('success error').addClass(type);
            alertBox.text(message);
            alertBox.slideDown();

            setTimeout(function () {
                alertBox.slideUp();
            }, 5000);
        }

        function simulateSuccess() {
            if (!currentTransactionId) {
                alert('Please create a payment first!');
                return;
            }

            $.post(baseUrl + 'payment/simulate/success', {
                transaction_id: currentTransactionId
            }, function (response) {
                showAlert('success', 'Payment marked as successful! Check the checkout page.');
            });
        }

        function simulateFailure() {
            if (!currentTransactionId) {
                alert('Please create a payment first!');
                return;
            }

            $.post(baseUrl + 'payment/simulate/failure', {
                transaction_id: currentTransactionId
            }, function (response) {
                showAlert('error', 'Payment marked as failed! Check the checkout page.');
            });
        }
    </script>
</body>

</html>