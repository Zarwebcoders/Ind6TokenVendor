<?php
// Simple test script to check API endpoint
header('Content-Type: application/json');

$url = 'http://localhost/api/payment/payraizen/initiate';
$data = [
    'vendor_id' => '1',
    'amount' => 100
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo json_encode([
    'http_code' => $httpCode,
    'curl_error' => $error,
    'response' => $response,
    'response_decoded' => json_decode($response, true)
], JSON_PRETTY_PRINT);
