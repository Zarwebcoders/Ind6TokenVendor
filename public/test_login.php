<!DOCTYPE html>
<html>

<head>
    <title>Login Test - Ind6TokenVendor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .test {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #666;
            margin-top: 30px;
        }

        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }

        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <h1>🔍 Login System Test</h1>

    <?php
    // Define FCPATH
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

    // Load CodeIgniter
    $pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
    $paths = require $pathsPath;
    require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

    $app = Config\Services::codeigniter();
    $app->initialize();

    echo '<div class="info"><strong>Environment:</strong> ' . ENVIRONMENT . '</div>';

    // Test 1: Database Connection
    echo '<h2>Test 1: Database Connection</h2>';
    try {
        $db = \Config\Database::connect();
        echo '<div class="success">✅ Database connected successfully</div>';
        echo '<div class="info">';
        echo 'Host: ' . $db->hostname . '<br>';
        echo 'Database: ' . $db->database . '<br>';
        echo 'Port: ' . $db->port . '<br>';
        echo 'Username: ' . $db->username . '<br>';
        echo '</div>';
    } catch (Exception $e) {
        echo '<div class="error">❌ Database connection failed: ' . $e->getMessage() . '</div>';
        echo '<p>Please check your database configuration in <code>app/Config/Database.php</code></p>';
        exit;
    }

    // Test 2: Check User
    echo '<h2>Test 2: User Account</h2>';
    try {
        $vendorModel = new \App\Models\VendorModel();
        $user = $vendorModel->where('email', 'admin@ind6token.com')->first();

        if ($user) {
            echo '<div class="success">✅ User account found</div>';
            echo '<div class="info">';
            echo 'ID: ' . $user['id'] . '<br>';
            echo 'Name: ' . $user['name'] . '<br>';
            echo 'Email: ' . $user['email'] . '<br>';
            echo 'Password: ' . htmlspecialchars($user['password']) . '<br>';
            echo 'Password Length: ' . strlen($user['password']) . ' characters<br>';
            echo '</div>';
        } else {
            echo '<div class="error">❌ User not found in database</div>';
            echo '<p>Run this SQL to create the user:</p>';
            echo '<code>INSERT INTO vendors (name, email, phone, password, wallet_address, created_at, updated_at) VALUES (\'Admin User\', \'admin@ind6token.com\', \'1234567890\', \'password123\', \'0x0000000000000000000000000000000000000000\', NOW(), NOW());</code>';
            exit;
        }
    } catch (Exception $e) {
        echo '<div class="error">❌ Error: ' . $e->getMessage() . '</div>';
        exit;
    }

    // Test 3: Password Test
    echo '<h2>Test 3: Password Verification</h2>';
    $testPassword = 'password123';
    $storedPassword = $user['password'];

    echo '<div class="info">';
    echo 'Test Password: <code>' . htmlspecialchars($testPassword) . '</code><br>';
    echo 'Stored Password: <code>' . htmlspecialchars($storedPassword) . '</code><br>';
    echo 'Test Length: ' . strlen($testPassword) . ' chars<br>';
    echo 'Stored Length: ' . strlen($storedPassword) . ' chars<br>';
    echo '</div>';

    if (trim($testPassword) === trim($storedPassword)) {
        echo '<div class="success">✅ Password matches!</div>';
    } else {
        echo '<div class="error">❌ Password does NOT match</div>';
        echo '<p>This is the problem! The stored password doesn\'t match "password123"</p>';
    }

    // Test 4: Session
    echo '<h2>Test 4: Session Test</h2>';
    $session = \Config\Services::session();
    $session->start();

    echo '<div class="info">';
    echo 'Session Driver: ' . get_class($session) . '<br>';
    echo 'Session ID: ' . $session->session_id . '<br>';
    echo '</div>';

    // Try setting and getting session
    $session->set('test_key', 'test_value');
    if ($session->get('test_key') === 'test_value') {
        echo '<div class="success">✅ Session working correctly</div>';
    } else {
        echo '<div class="error">❌ Session not working</div>';
    }

    // Test 5: Routes
    echo '<h2>Test 5: URL Configuration</h2>';
    echo '<div class="info">';
    echo 'Base URL: <code>' . base_url() . '</code><br>';
    echo 'Login URL: <code>' . base_url('auth/login') . '</code><br>';
    echo 'Dashboard URL: <code>' . base_url('') . '</code><br>';
    echo '</div>';

    // Test 6: Check logs
    echo '<h2>Test 6: Log Files</h2>';
    $logPath = WRITEPATH . 'logs/log-' . date('Y-m-d') . '.php';
    if (file_exists($logPath)) {
        echo '<div class="success">✅ Log file exists: <code>' . $logPath . '</code></div>';
        echo '<div class="info">Check this file for login attempt logs after trying to login</div>';
    } else {
        echo '<div class="info">ℹ️ No log file yet. Will be created on first login attempt.</div>';
    }

    ?>

    <h2>Next Steps</h2>
    <div class="info">
        <p><strong>If all tests above passed:</strong></p>
        <ol>
            <li>Click the "Go to Login" button below</li>
            <li>Enter the credentials shown above</li>
            <li>Click "Sign in"</li>
            <li>If it doesn't work, come back here and click "View Logs"</li>
        </ol>
    </div>

    <div style="margin-top: 30px;">
        <a href="<?= base_url('auth/login') ?>" class="btn">🔐 Go to Login Page</a>
        <a href="<?= base_url() ?>" class="btn">🏠 Go to Dashboard</a>
    </div>

    <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
        <strong>Login Credentials:</strong><br>
        Email: <code>admin@ind6token.com</code><br>
        Password: <code>password123</code>
    </div>
</body>

</html>