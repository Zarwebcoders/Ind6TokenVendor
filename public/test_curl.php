<?php
// Test PHP cURL configuration
echo "<h2>PHP cURL Diagnostic Test</h2>";

// Check if cURL is enabled
if (!function_exists('curl_version')) {
    die("cURL is NOT enabled in PHP!");
}

echo "<h3>✓ cURL is enabled</h3>";

// Get cURL version info
$version = curl_version();
echo "<pre>";
echo "cURL Version: " . $version['version'] . "\n";
echo "SSL Version: " . $version['ssl_version'] . "\n";
echo "libz Version: " . $version['libz_version'] . "\n";
echo "Host: " . $version['host'] . "\n";
echo "Features: " . $version['features'] . "\n";
echo "Protocols: " . implode(', ', $version['protocols']) . "\n";
echo "</pre>";

// Test connection to Payraizen
echo "<h3>Testing Connection to Payraizen...</h3>";

$url = 'https://partner.payraizen.com/tech/api/payin/create_intent';
$data = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'mobile' => '9876543210',
    'amount' => 100,
    'mid' => '987654321'
];

$headers = [
    'Authorization: Bearer bnsgwvYeeTnFHA72YkiZ7RJEw0WgtO7cBbV8JcFK',
    'Content-Type: application/json',
    'accept: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Timeout settings
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// SSL settings - try without verification first
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

// Verbose output
curl_setopt($ch, CURLOPT_VERBOSE, true);
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

echo "<p>Sending request...</p>";
$start = microtime(true);
$response = curl_exec($ch);
$duration = microtime(true) - $start;

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
$curlErrno = curl_errno($ch);
$curlInfo = curl_getinfo($ch);

// Get verbose output
rewind($verbose);
$verboseLog = stream_get_contents($verbose);

curl_close($ch);

echo "<h3>Results:</h3>";
echo "<pre>";
echo "Duration: " . round($duration, 2) . " seconds\n";
echo "HTTP Code: " . $httpCode . "\n";
echo "cURL Error: " . ($curlError ?: 'None') . "\n";
echo "cURL Error Number: " . $curlErrno . "\n\n";

echo "Connection Info:\n";
echo "  Total Time: " . $curlInfo['total_time'] . " seconds\n";
echo "  DNS Lookup Time: " . $curlInfo['namelookup_time'] . " seconds\n";
echo "  Connect Time: " . $curlInfo['connect_time'] . " seconds\n";
echo "  Pre-transfer Time: " . $curlInfo['pretransfer_time'] . " seconds\n";
echo "  Start Transfer Time: " . $curlInfo['starttransfer_time'] . " seconds\n";
echo "  Redirect Time: " . $curlInfo['redirect_time'] . " seconds\n";
echo "\n";

if ($response) {
    echo "Response:\n";
    echo $response . "\n\n";

    $decoded = json_decode($response, true);
    if ($decoded) {
        echo "Decoded Response:\n";
        print_r($decoded);
    }
} else {
    echo "No response received.\n";
}

echo "\nVerbose Log:\n";
echo $verboseLog;
echo "</pre>";

// Test with SSL verification enabled
echo "<hr><h3>Testing with SSL Verification Enabled...</h3>";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch2, CURLOPT_TIMEOUT, 30);

// SSL verification ON
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 2);

$start2 = microtime(true);
$response2 = curl_exec($ch2);
$duration2 = microtime(true) - $start2;

$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$curlError2 = curl_error($ch2);
$curlErrno2 = curl_errno($ch2);

curl_close($ch2);

echo "<pre>";
echo "Duration: " . round($duration2, 2) . " seconds\n";
echo "HTTP Code: " . $httpCode2 . "\n";
echo "cURL Error: " . ($curlError2 ?: 'None') . "\n";
echo "cURL Error Number: " . $curlErrno2 . "\n";

if ($response2) {
    echo "\nResponse: " . $response2 . "\n";
} else {
    echo "\nNo response received.\n";
}
echo "</pre>";

echo "<h3>PHP Configuration:</h3>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . " seconds\n";
echo "default_socket_timeout: " . ini_get('default_socket_timeout') . " seconds\n";
echo "</pre>";
