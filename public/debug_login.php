<?php
// Debug Login Test
// Access this file at: http://localhost:8888/Ind6TokenVendor/debug_login.php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap CodeIgniter
$app = require_once FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require_once SYSTEMPATH . 'bootstrap.php';

echo "<h1>Login Debug Test</h1>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
try {
    $db = \Config\Database::connect();
    echo "✅ Database connected successfully<br>";
    echo "Database: " . $db->database . "<br>";
    echo "Port: " . $db->port . "<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Check if vendors table exists
echo "<h2>Test 2: Check Vendors Table</h2>";
try {
    $query = $db->query("SHOW TABLES LIKE 'vendors'");
    if ($query->getNumRows() > 0) {
        echo "✅ Vendors table exists<br>";
    } else {
        echo "❌ Vendors table does not exist<br>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Check if user exists
echo "<h2>Test 3: Check User Exists</h2>";
try {
    $vendorModel = new \App\Models\VendorModel();
    $user = $vendorModel->where('email', 'admin@ind6token.com')->first();

    if ($user) {
        echo "✅ User found<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Name: " . $user['name'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Password (stored): " . $user['password'] . "<br>";
    } else {
        echo "❌ User not found<br>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Error finding user: " . $e->getMessage() . "<br>";
    exit;
}

// Test 4: Test password comparison
echo "<h2>Test 4: Password Comparison</h2>";
$testPassword = 'password123';
echo "Test password: " . $testPassword . "<br>";
echo "Stored password: " . $user['password'] . "<br>";

if ($testPassword == $user['password']) {
    echo "✅ Password matches (plain text comparison)<br>";
} else {
    echo "❌ Password does NOT match<br>";
    echo "Test password length: " . strlen($testPassword) . "<br>";
    echo "Stored password length: " . strlen($user['password']) . "<br>";
}

// Test 5: Test session
echo "<h2>Test 5: Session Test</h2>";
$session = \Config\Services::session();
echo "Session ID: " . $session->session_id . "<br>";

// Try setting session data
$session->set([
    'test_id' => 123,
    'test_name' => 'Test User',
    'isLoggedIn' => true
]);

if ($session->get('isLoggedIn')) {
    echo "✅ Session can be set and retrieved<br>";
} else {
    echo "❌ Session cannot be set<br>";
}

// Test 6: Test redirect
echo "<h2>Test 6: Base URL Test</h2>";
echo "Base URL: " . base_url() . "<br>";
echo "Base URL(''): " . base_url('') . "<br>";
echo "Base URL('auth/login'): " . base_url('auth/login') . "<br>";

echo "<h2>Summary</h2>";
echo "If all tests pass above, the login should work. Try logging in again.<br>";
echo "<a href='" . base_url('auth/login') . "'>Go to Login Page</a>";
