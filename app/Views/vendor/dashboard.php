<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Vendor Panel</title>
    <style>
        :root {
            --primary: #6366f1;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
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
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .main {
            flex: 1;
            padding: 40px;
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
            border-bottom: 2px solid transparent;
        }

        .nav-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, min-min-content, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        th {
            background: #f9fafb;
            color: var(--text-muted);
            font-weight: 600;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-success {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef9c3;
            color: #854d0e;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="main">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1>Welcome,
                    <?= $vendor['name'] ?>
                </h1>
                <a href="<?= base_url('auth/logout') ?>" style="color: #ef4444; font-weight: 600;">Logout</a>
            </div>

            <div class="nav-tabs">
                <a href="<?= base_url('vendor/dashboard') ?>" class="nav-tab active">Overview</a>
                <a href="<?= base_url('vendor/api-settings') ?>" class="nav-tab">API Keys</a>
                <a href="<?= base_url('vendor/api-docs') ?>" class="nav-tab">Documentation</a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Transactions</div>
                    <div class="stat-value">
                        <?= $total_payments ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Successful Payments</div>
                    <div class="stat-value">
                        <?= $success_payments ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Recent Transactions</div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>UTR / Reference</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_payments as $p): ?>
                            <tr>
                                <td>
                                    <?= date('d M, Y H:i', strtotime($p['created_at'])) ?>
                                </td>
                                <td>
                                    <?= $p['txn_id'] ?>
                                </td>
                                <td>₹
                                    <?= $p['amount'] ?>
                                </td>
                                <td>
                                    <?= $p['utr'] ?: '-' ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $p['status'] ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent_payments)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted);">No transactions found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>