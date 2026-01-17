<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation | Developer Guide</title>
    <style>
        :root {
            --primary: #6366f1;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --code-bg: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            margin-bottom: 50px;
            text-align: center;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .header p {
            color: var(--text-muted);
            font-size: 18px;
        }

        .section {
            margin-bottom: 60px;
        }

        .section h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
            padding-bottom: 5px;
        }

        .endpoint {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 25px;
            margin-bottom: 30px;
        }

        .method-badge {
            background: #6366f1;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
            margin-right: 10px;
        }

        .url {
            font-family: monospace;
            font-weight: 600;
            color: #475569;
        }

        .param-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        .param-table th,
        .param-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid var(--border);
        }

        .param-table th {
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.1em;
        }

        .required {
            color: #ef4444;
            font-weight: bold;
        }

        pre {
            background: var(--code-bg);
            color: #38bdf8;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            font-size: 14px;
            margin: 15px 0;
        }

        .nav-links {
            margin-bottom: 40px;
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="nav-links">
            <a href="<?= base_url('vendor/dashboard') ?>">← Back to Dashboard</a>
            <a href="<?= base_url('vendor/api-settings') ?>">API Keys</a>
        </div>

        <div class="header">
            <h1>Vendor API v1.0</h1>
            <p>Integrate our payment collection engine into your own application.</p>
        </div>

        <div class="section">
            <h2>Authentication</h2>
            <p>All API requests must include your Bearer Token in the HTTP headers. You can generate this token from
                your dashboard settings.</p>
            <pre>Authorization: Bearer YOUR_API_TOKEN</pre>
            <div class="info-box">
                <strong>Tip:</strong> Ensure you have whitelisted your server's IP in the settings to avoid 403
                Forbidden errors.
            </div>
        </div>

        <div class="section">
            <h2>1. Create Payment</h2>
            <p>Initiate a new UPI payment request. This will return a <code>payment_url</code> which you can show to
                your customer as a QR code or an intent link.</p>

            <div class="endpoint">
                <span class="method-badge">POST</span>
                <span class="url">/api/v1/payment/create</span>

                <table class="param-table">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>amount</code> <span class="required">*</span></td>
                            <td>Float</td>
                            <td>The amount to collect in INR.</td>
                        </tr>
                        <tr>
                            <td><code>customer_name</code> <span class="required">*</span></td>
                            <td>String</td>
                            <td>The name of the payer.</td>
                        </tr>
                        <tr>
                            <td><code>customer_mobile</code></td>
                            <td>String</td>
                            <td>10-digit mobile number.</td>
                        </tr>
                        <tr>
                            <td><code>customer_email</code></td>
                            <td>String</td>
                            <td>Email address of the payer.</td>
                        </tr>
                        <tr>
                            <td><code>customer_id</code></td>
                            <td>String</td>
                            <td>Your internal reference for this user.</td>
                        </tr>
                    </tbody>
                </table>

                <strong>Request Example:</strong>
                <pre>
{
    "amount": 500.00,
    "customer_name": "John Doe",
    "customer_id": "ORDER_123456"
}</pre>

                <strong>Success Response:</strong>
                <pre>
{
    "success": true,
    "order_id": "KAY176864...",
    "payment_url": "upi://pay?pa=...",
    "message": "Transaction created successfully"
}</pre>
            </div>
        </div>

        <div class="section">
            <h2>2. Check Status</h2>
            <p>Verify the status of a specific transaction manually.</p>
            <div class="endpoint">
                <span class="method-badge">POST</span>
                <span class="url">/api/v1/payment/status</span>

                <table class="param-table">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>order_id</code> <span class="required">*</span></td>
                            <td>String</td>
                            <td>The Order ID returned during creation.</td>
                        </tr>
                    </tbody>
                </table>

                <strong>Success Response:</strong>
                <pre>
{
    "success": true,
    "status": "success",
    "utr": "REF260117...",
    "message": "Payment verified successfully"
}</pre>
            </div>
        </div>

        <div class="section">
            <h2>3. Webhooks</h2>
            <p>If you configure a <code>Webhook URL</code> in your settings, our server will send a POST request to you
                as soon as a payment is completed.</p>
            <strong>Payload delivered to you:</strong>
            <pre>
{
    "order_id": "KAY123456",
    "amount": 500.00,
    "status": "success",
    "utr": "REF987654321",
    "timestamp": "2026-01-17 18:30:00"
}</pre>
        </div>
    </div>
</body>

</html>