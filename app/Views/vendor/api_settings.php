<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Settings | Developer Dashboard</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar sidebar styling would go here, omitting for brevity in test view */

        .main-content {
            flex: 1;
            padding: 40px;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
        }

        .header p {
            color: var(--text-muted);
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="url"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            background: #f1f5f9;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
        }

        .token-display {
            background: #1e293b;
            color: #38bdf8;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
            position: relative;
            margin-bottom: 10px;
            word-break: break-all;
        }

        .copy-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: 0.2s;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            background: var(--bg);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .nav-tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border);
        }

        .nav-tab {
            padding: 10px 0;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            border-bottom: 2px solid transparent;
        }

        .nav-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-live {
            background: #dcfce7;
            color: #166534;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="main-content">
            <div class="header">
                <h1>Developer Settings</h1>
                <p>Manage your API credentials and webhook configurations.</p>
            </div>

            <div class="nav-tabs">
                <a href="<?= base_url('vendor/dashboard') ?>" class="nav-tab">Overview</a>
                <a href="<?= base_url('vendor/api-settings') ?>" class="nav-tab active">API Keys</a>
                <a href="<?= base_url('vendor/api-docs') ?>" class="nav-tab">Documentation</a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Production API Keys</div>
                    <span class="badge badge-live">Active</span>
                </div>

                <div class="form-group">
                    <label>Bearer Token</label>
                    <div class="token-display">
                        <span id="tokenText">
                            <?= $vendor['api_token'] ?: 'No token generated yet' ?>
                        </span>
                        <button class="copy-btn" onclick="copyToClipboard('tokenText')">Copy</button>
                    </div>
                    <p style="font-size: 12px; color: var(--text-muted);">Use this token in your
                        <code>Authorization: Bearer</code> header.</p>
                </div>

                <form action="<?= base_url('vendor/api-settings/update') ?>" method="POST">
                    <input type="hidden" name="action" value="generate_token">
                    <button type="submit" class="btn btn-outline">Generate New Token</button>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Security & Webhooks</div>
                </div>

                <form action="<?= base_url('vendor/api-settings/update') ?>" method="POST">
                    <input type="hidden" name="action" value="update_ips">

                    <div class="form-group">
                        <label for="whitelisted_ips">Whitelisted IP Addresses</label>
                        <input type="text" id="whitelisted_ips" name="whitelisted_ips"
                            value="<?= $vendor['whitelisted_ips'] ?>" placeholder="e.g. 192.168.1.1, 203.0.113.5">
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Comma separated list of
                            IPs allowed to call the API. Leave empty to allow all IPs.</p>
                    </div>

                    <div class="form-group">
                        <label for="webhook_url">Webhook Notification URL</label>
                        <input type="url" id="webhook_url" name="webhook_url" value="<?= $vendor['webhook_url'] ?>"
                            placeholder="https://your-app.com/api/payment-webhook">
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">We will send a POST
                            request to this URL whenever a payment is successful.</p>
                    </div>

                    <button type="submit" class="btn btn-primary">Save API Configuration</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(id) {
            const text = document.getElementById(id).innerText;
            if (text.includes('No token')) return;
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard!');
            });
        }
    </script>
</body>

</html>