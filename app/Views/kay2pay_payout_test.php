<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kay2Pay Payout Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            max-width: 600px;
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

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2d3436;
            font-size: 28px;
            font-weight: 700;
        }

        .header p {
            color: #636e72;
            margin-top: 5px;
        }

        .badge {
            background: #e17055;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
            margin-top: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full-width {
            grid-column: 1 / span 2;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #2d3436;
            font-weight: 600;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #edf2f7;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(118, 75, 162, 0.3);
        }

        .submit-btn:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
        }

        #result {
            margin-top: 25px;
            padding: 20px;
            border-radius: 10px;
            display: none;
            animation: fadeIn 0.3s ease;
            white-space: pre-wrap;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            overflow-x: auto;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block !important;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .loader {
            display: none;
            text-align: center;
            margin-top: 15px;
        }

        .spinner {
            width: 30px;
            height: 30px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #764ba2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>💸 Kay2Pay Payout</h1>
            <p>Automated Payout Testing Interface</p>
            <span class="badge">Bank Transfer</span>
        </div>

        <form id="payoutForm">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name">Beneficiary Name</label>
                    <input type="text" id="name" name="name" placeholder="Full name as per bank" required>
                </div>

                <div class="form-group">
                    <label for="account_number">Account Number</label>
                    <input type="text" id="account_number" name="account_number" placeholder="Enter bank account no"
                        required>
                </div>

                <div class="form-group">
                    <label for="ifsc_code">IFSC Code</label>
                    <input type="text" id="ifsc_code" name="ifsc_code" placeholder="Example: SBIN0001234" required>
                </div>

                <div class="form-group">
                    <label for="bank_name">Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name" placeholder="Example: State Bank of India"
                        required>
                </div>

                <div class="form-group">
                    <label for="amount">Payout Amount (INR)</label>
                    <input type="number" id="amount" name="amount" placeholder="0.00" step="0.01" min="1" required>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">Proceed to Payout</button>
        </form>

        <div class="loader" id="loader">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: #636e72;">Processing Payout...</p>
        </div>

        <div id="result"></div>
    </div>

    <script>
        const BASE_URL = '<?= base_url() ?>'.replace(/\/$/, '') + '/index.php/';
        const form = document.getElementById('payoutForm');
        const submitBtn = document.getElementById('submitBtn');
        const loader = document.getElementById('loader');
        const resultDiv = document.getElementById('result');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Reset UI
            submitBtn.disabled = true;
            loader.style.display = 'block';
            resultDiv.style.display = 'none';
            resultDiv.className = '';

            const formData = {
                name: document.getElementById('name').value,
                account_number: document.getElementById('account_number').value,
                ifsc_code: document.getElementById('ifsc_code').value,
                bank_name: document.getElementById('bank_name').value,
                amount: parseFloat(document.getElementById('amount').value),
                vendor_id: 1 // Default test vendor
            };

            try {
                const response = await fetch(`${BASE_URL}api/kay2pay/payout/initiate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                loader.style.display = 'none';
                submitBtn.disabled = false;
                resultDiv.style.display = 'block';

                if (data.success) {
                    resultDiv.classList.add('success');
                    resultDiv.textContent = `✅ Success!\n\nMessage: ${data.message}\nTxn ID: ${data.txn_id}\nUTR: ${data.utr || 'Pending'}\nStatus: ${data.status}`;
                } else {
                    resultDiv.classList.add('error');
                    resultDiv.textContent = `❌ Failed!\n\nError: ${data.message}\n\nRaw Response:\n${JSON.stringify(data.raw || {}, null, 2)}`;
                }

            } catch (error) {
                console.error('Error:', error);
                loader.style.display = 'none';
                submitBtn.disabled = false;
                resultDiv.style.display = 'block';
                resultDiv.classList.add('error');
                resultDiv.textContent = 'Critical Error: ' + error.message;
            }
        });
    </script>
</body>

</html>