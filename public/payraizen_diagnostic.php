<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayRaizen Connection Test</title>
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
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .test-section h2 {
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .result {
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .result.success {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            color: #155724;
        }

        .result.error {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
        }

        .result.info {
            background: #d1ecf1;
            border: 2px solid #bee5eb;
            color: #0c5460;
        }

        .btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔍 PayRaizen Connection Diagnostic</h1>
        <p class="subtitle">Testing connectivity to PayRaizen API endpoint</p>

        <?php
        // Test 1: DNS Resolution
        echo '<div class="test-section">';
        echo '<h2>Test 1: DNS Resolution</h2>';
        $host = 'partner.payraizen.com';
        $ip = gethostbyname($host);

        if ($ip !== $host) {
            echo '<div class="result success">';
            echo "✓ DNS Resolution Successful\n";
            echo "Host: {$host}\n";
            echo "IP Address: {$ip}";
            echo '</div>';
        } else {
            echo '<div class="result error">';
            echo "✗ DNS Resolution Failed\n";
            echo "Host: {$host}\n";
            echo "Could not resolve hostname to IP address";
            echo '</div>';
        }
        echo '</div>';

        // Test 2: Basic cURL Connection
        echo '<div class="test-section">';
        echo '<h2>Test 2: Basic HTTPS Connection</h2>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://partner.payraizen.com');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        $curlInfo = curl_getinfo($ch);
        curl_close($ch);

        if ($curlError) {
            echo '<div class="result error">';
            echo "✗ Connection Failed\n";
            echo "Error: {$curlError}\n";
            echo "Error Code: {$curlErrno}\n";
            echo "Total Time: " . ($curlInfo['total_time'] ?? 'N/A') . " seconds\n";
            echo "Name Lookup Time: " . ($curlInfo['namelookup_time'] ?? 'N/A') . " seconds\n";
            echo "Connect Time: " . ($curlInfo['connect_time'] ?? 'N/A') . " seconds";
            echo '</div>';
        } else {
            echo '<div class="result success">';
            echo "✓ Connection Successful\n";
            echo "HTTP Code: {$httpCode}\n";
            echo "Total Time: " . round($curlInfo['total_time'], 2) . " seconds\n";
            echo "Connect Time: " . round($curlInfo['connect_time'], 2) . " seconds";
            echo '</div>';
        }
        echo '</div>';

        // Test 3: SSL Certificate Check
        echo '<div class="test-section">';
        echo '<h2>Test 3: SSL Certificate Verification</h2>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://partner.payraizen.com');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo '<div class="result error">';
            echo "✗ Connection Failed Even Without SSL Verification\n";
            echo "Error: {$curlError}\n";
            echo "This suggests a network/firewall issue, not an SSL issue";
            echo '</div>';
        } else {
            echo '<div class="result info">';
            echo "ℹ Connection Works Without SSL Verification\n";
            echo "This might indicate an SSL certificate issue.\n";
            echo "Try updating your CA certificates or disabling SSL verification for testing.";
            echo '</div>';
        }
        echo '</div>';

        // Test 4: API Endpoint Test
        echo '<div class="test-section">';
        echo '<h2>Test 4: API Endpoint Accessibility</h2>';

        $apiUrl = 'https://partner.payraizen.com/tech/api/payin/create_intent';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            echo '<div class="result error">';
            echo "✗ API Endpoint Not Reachable\n";
            echo "URL: {$apiUrl}\n";
            echo "Error: {$curlError}";
            echo '</div>';
        } else {
            echo '<div class="result success">';
            echo "✓ API Endpoint Reachable\n";
            echo "URL: {$apiUrl}\n";
            echo "HTTP Code: {$httpCode}";
            echo '</div>';
        }
        echo '</div>';

        // Test 5: PHP Configuration
        echo '<div class="test-section">';
        echo '<h2>Test 5: PHP Configuration</h2>';

        echo '<div class="result info">';
        echo "PHP Version: " . phpversion() . "\n";
        echo "cURL Version: " . curl_version()['version'] . "\n";
        echo "SSL Version: " . curl_version()['ssl_version'] . "\n";
        echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled') . "\n";
        echo "OpenSSL: " . (extension_loaded('openssl') ? 'Loaded' : 'Not Loaded') . "\n";
        echo "cURL: " . (extension_loaded('curl') ? 'Loaded' : 'Not Loaded');
        echo '</div>';
        echo '</div>';
        ?>

        <div class="test-section">
            <h2>Recommendations</h2>
            <div class="result info">
                Based on the test results above:

                1. If DNS resolution fails:
                - Check your internet connection
                - Try using a different DNS server (8.8.8.8 or 1.1.1.1)

                2. If connection fails with timeout:
                - Check your firewall settings
                - Ensure MAMP allows outgoing HTTPS connections
                - Try disabling antivirus temporarily

                3. If SSL verification fails:
                - Update your CA certificates
                - For testing only, you can disable SSL verification in PaymentApi.php:
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

                4. If all tests fail:
                - Contact your network administrator
                - Check if PayRaizen API is currently operational
                - Try accessing from a different network
            </div>
        </div>
    </div>
</body>

</html>