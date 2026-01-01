<?php
/**
 * IP Address Checker
 * Use this to find your server's IP address for LocalPaisa whitelist
 * 
 * Visit: https://ind6vendorfinal.zarwebcoders.in/check_ip.php
 * Delete this file after use for security
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Server IP Check - LocalPaisa Whitelist</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }

        .ip-box {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .ip-box.primary {
            background: #fff3e0;
            border-left-color: #ff9800;
        }

        .ip-value {
            font-size: 24px;
            font-weight: bold;
            color: #2196F3;
            font-family: 'Courier New', monospace;
        }

        .label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }

        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }

        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: bold;
        }

        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }

        .btn:hover {
            background: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🌐 Server IP Address Check</h1>
        <p>Use this information to whitelist your server IP in LocalPaisa dashboard</p>

        <?php
        // Get all IP information
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? 'Not available';
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? 'Not available';
        $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'Not set';
        $clientIP = $_SERVER['HTTP_CLIENT_IP'] ?? 'Not set';

        // Get public IP
        $publicIP = 'Could not fetch';
        try {
            $publicIPData = @file_get_contents('https://api.ipify.org?format=json');
            if ($publicIPData) {
                $publicIPJson = json_decode($publicIPData, true);
                $publicIP = $publicIPJson['ip'] ?? 'Could not fetch';
            }
        } catch (Exception $e) {
            // Silently fail
        }

        // Get IP details
        $ipDetails = null;
        try {
            $ipDetailsData = @file_get_contents('https://ipinfo.io/json');
            if ($ipDetailsData) {
                $ipDetails = json_decode($ipDetailsData, true);
            }
        } catch (Exception $e) {
            // Silently fail
        }
        ?>

        <div class="ip-box primary">
            <div class="label">🎯 PRIMARY IP TO WHITELIST:</div>
            <div class="ip-value">
                <?= htmlspecialchars($publicIP) ?>
            </div>
            <p style="margin-top: 10px; color: #666;">
                <strong>This is your public IP address.</strong> Add this to LocalPaisa whitelist.
            </p>
        </div>

        <div class="success">
            <strong>✅ What to do:</strong>
            <ol>
                <li>Copy the IP address above: <code><?= htmlspecialchars($publicIP) ?></code></li>
                <li>Login to LocalPaisa Merchant Dashboard</li>
                <li>Go to Settings → API Configuration → IP Whitelist</li>
                <li>Add this IP address</li>
                <li>Save and wait 5-10 minutes</li>
                <li>Test your payment again</li>
            </ol>
        </div>

        <h2>📊 All IP Information</h2>
        <table>
            <tr>
                <th>Type</th>
                <th>Value</th>
                <th>Description</th>
            </tr>
            <tr>
                <td><strong>Public IP</strong></td>
                <td><code><?= htmlspecialchars($publicIP) ?></code></td>
                <td>✅ Use this for whitelist</td>
            </tr>
            <tr>
                <td>Server Address</td>
                <td><code><?= htmlspecialchars($serverAddr) ?></code></td>
                <td>Internal server IP</td>
            </tr>
            <tr>
                <td>Remote Address</td>
                <td><code><?= htmlspecialchars($remoteAddr) ?></code></td>
                <td>Your current IP</td>
            </tr>
            <tr>
                <td>X-Forwarded-For</td>
                <td><code><?= htmlspecialchars($forwardedFor) ?></code></td>
                <td>Proxy/CDN IP (if any)</td>
            </tr>
            <tr>
                <td>Client IP</td>
                <td><code><?= htmlspecialchars($clientIP) ?></code></td>
                <td>Client header IP</td>
            </tr>
        </table>

        <?php if ($ipDetails): ?>
            <h2>🌍 IP Geolocation Details</h2>
            <table>
                <tr>
                    <th>Property</th>
                    <th>Value</th>
                </tr>
                <?php foreach ($ipDetails as $key => $value): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) ?>
                        </td>
                        <td><code><?= htmlspecialchars(is_array($value) ? json_encode($value) : $value) ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <div class="warning">
            <strong>⚠️ Security Note:</strong>
            <p>Delete this file after you've copied the IP address!</p>
            <p>This file exposes server information and should not be left accessible.</p>
        </div>

        <div class="info">
            <h3>📝 Next Steps:</h3>
            <ol>
                <li><strong>Copy IP:</strong> <code><?= htmlspecialchars($publicIP) ?></code></li>
                <li><strong>Login to LocalPaisa:</strong> Visit your merchant dashboard</li>
                <li><strong>Whitelist IP:</strong> Add to API settings</li>
                <li><strong>Test:</strong> Try payment again after 10 minutes</li>
                <li><strong>Delete this file:</strong> Remove <code>check_ip.php</code> for security</li>
            </ol>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="/payment/test" class="btn">Test Payment</a>
            <a href="/" class="btn" style="background: #2196F3;">Back to Dashboard</a>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h3>🔧 Raw Server Variables (for debugging)</h3>
            <pre style="background: white; padding: 15px; border-radius: 5px; overflow-x: auto;"><?php
            $serverVars = [
                'SERVER_ADDR' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
                'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
                'HTTP_X_FORWARDED_FOR' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'N/A',
                'HTTP_CLIENT_IP' => $_SERVER['HTTP_CLIENT_IP'] ?? 'N/A',
                'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? 'N/A',
                'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'N/A',
            ];
            echo json_encode($serverVars, JSON_PRETTY_PRINT);
            ?></pre>
        </div>
    </div>
</body>

</html>