<!DOCTYPE html>
<html>

<head>
    <title>Simple Login Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 30px auto;
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
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }

        h2 {
            color: #555;
            margin-top: 30px;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }

        .test {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #ccc;
        }

        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }

        .info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }

        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }

        code {
            background: #f4f4f4;
            padding: 3px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: bold;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: bold;
        }

        .credential-box {
            background: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            border: 2px solid #ffc107;
            margin: 20px 0;
        }

        .credential-box h3 {
            margin-top: 0;
            color: #856404;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔍 Simple Login Diagnostic Test</h1>
        <p>This page tests your database connection and login credentials without loading the full CodeIgniter
            framework.</p>

        <?php
        // Configuration
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = 'root';
        $dbName = 'ind6token_admin';
        $dbPort = 8889;

        $testEmail = 'admin@ind6token.com';
        $testPassword = 'password123';

        // Test 1: Database Connection
        echo '<h2>Test 1: Database Connection</h2>';
        $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

        if ($mysqli->connect_error) {
            echo '<div class="error">';
            echo '<strong>❌ Database Connection Failed</strong><br>';
            echo 'Error: ' . $mysqli->connect_error . '<br>';
            echo 'Error Code: ' . $mysqli->connect_errno . '<br><br>';
            echo '<strong>Check these settings:</strong><br>';
            echo 'Host: ' . $dbHost . '<br>';
            echo 'Port: ' . $dbPort . '<br>';
            echo 'Username: ' . $dbUser . '<br>';
            echo 'Password: ' . $dbPass . '<br>';
            echo 'Database: ' . $dbName . '<br>';
            echo '</div>';
            echo '<div class="warning">Make sure MAMP is running and MySQL is started on port 8889</div>';
            exit;
        }

        echo '<div class="success">';
        echo '<strong>✅ Database Connected Successfully</strong><br>';
        echo 'Host: ' . $dbHost . ':' . $dbPort . '<br>';
        echo 'Database: ' . $dbName . '<br>';
        echo 'Character Set: ' . $mysqli->character_set_name() . '<br>';
        echo '</div>';

        // Test 2: Check if vendors table exists
        echo '<h2>Test 2: Vendors Table</h2>';
        $result = $mysqli->query("SHOW TABLES LIKE 'vendors'");

        if ($result->num_rows === 0) {
            echo '<div class="error">';
            echo '<strong>❌ Vendors Table Does Not Exist</strong><br>';
            echo 'The vendors table is missing from the database.<br>';
            echo 'Run migrations to create it.';
            echo '</div>';
            exit;
        }

        echo '<div class="success">✅ Vendors table exists</div>';

        // Get table structure
        $structure = $mysqli->query("DESCRIBE vendors");
        echo '<div class="info">';
        echo '<strong>Table Structure:</strong><br>';
        echo '<table>';
        echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>';
        while ($row = $structure->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['Field'] . '</td>';
            echo '<td>' . $row['Type'] . '</td>';
            echo '<td>' . $row['Null'] . '</td>';
            echo '<td>' . $row['Key'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // Test 3: Check if user exists
        echo '<h2>Test 3: User Account</h2>';
        $stmt = $mysqli->prepare("SELECT id, name, email, phone, password, status FROM vendors WHERE email = ?");
        $stmt->bind_param('s', $testEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            echo '<div class="error">';
            echo '<strong>❌ User Account Not Found</strong><br>';
            echo 'No user found with email: <code>' . htmlspecialchars($testEmail) . '</code><br><br>';
            echo '<strong>Create the user with this SQL:</strong>';
            echo '<pre>INSERT INTO vendors (name, email, phone, password, wallet_address, created_at, updated_at) 
VALUES (\'Admin User\', \'admin@ind6token.com\', \'1234567890\', \'password123\', \'0x0000000000000000000000000000000000000000\', NOW(), NOW());</pre>';
            echo '</div>';
            exit;
        }

        echo '<div class="success">✅ User account found</div>';
        echo '<div class="info">';
        echo '<strong>User Details:</strong><br>';
        echo '<table>';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>ID</td><td>' . $user['id'] . '</td></tr>';
        echo '<tr><td>Name</td><td>' . htmlspecialchars($user['name']) . '</td></tr>';
        echo '<tr><td>Email</td><td>' . htmlspecialchars($user['email']) . '</td></tr>';
        echo '<tr><td>Phone</td><td>' . htmlspecialchars($user['phone']) . '</td></tr>';
        echo '<tr><td>Password</td><td><code>' . htmlspecialchars($user['password']) . '</code></td></tr>';
        echo '<tr><td>Password Length</td><td>' . strlen($user['password']) . ' characters</td></tr>';
        echo '<tr><td>Status</td><td>' . $user['status'] . '</td></tr>';
        echo '</table>';
        echo '</div>';

        // Test 4: Password Comparison
        echo '<h2>Test 4: Password Verification</h2>';

        $storedPassword = $user['password'];
        $inputPassword = $testPassword;

        echo '<div class="info">';
        echo '<strong>Password Comparison:</strong><br>';
        echo '<table>';
        echo '<tr><th>Type</th><th>Value</th><th>Length</th><th>Hex</th></tr>';
        echo '<tr>';
        echo '<td>Input Password</td>';
        echo '<td><code>' . htmlspecialchars($inputPassword) . '</code></td>';
        echo '<td>' . strlen($inputPassword) . '</td>';
        echo '<td>' . bin2hex($inputPassword) . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>Stored Password</td>';
        echo '<td><code>' . htmlspecialchars($storedPassword) . '</code></td>';
        echo '<td>' . strlen($storedPassword) . '</td>';
        echo '<td>' . bin2hex($storedPassword) . '</td>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';

        // Test with trim
        $trimmedInput = trim($inputPassword);
        $trimmedStored = trim($storedPassword);

        if ($trimmedInput === $trimmedStored) {
            echo '<div class="success">';
            echo '<strong>✅ PASSWORD MATCH!</strong><br>';
            echo 'The password comparison works correctly.<br>';
            echo 'Login should work if session is configured properly.';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<strong>❌ PASSWORD MISMATCH!</strong><br>';
            echo 'This is the problem! The passwords don\'t match.<br><br>';
            echo '<strong>Details:</strong><br>';
            echo 'Input: "' . $trimmedInput . '" (length: ' . strlen($trimmedInput) . ')<br>';
            echo 'Stored: "' . $trimmedStored . '" (length: ' . strlen($trimmedStored) . ')<br><br>';
            echo '<strong>Fix with this SQL:</strong>';
            echo '<pre>UPDATE vendors SET password = \'password123\' WHERE email = \'admin@ind6token.com\';</pre>';
            echo '</div>';
        }

        // Test 5: Session Configuration
        echo '<h2>Test 5: Session Test</h2>';

        // Check if session can start
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            echo '<div class="success">✅ PHP Session can be started</div>';

            // Test setting and getting session
            $_SESSION['test_key'] = 'test_value_' . time();

            if (isset($_SESSION['test_key']) && $_SESSION['test_key'] === 'test_value_' . time()) {
                echo '<div class="success">✅ Session data can be set and retrieved</div>';
            } else {
                echo '<div class="warning">⚠️ Session data might not persist</div>';
            }

            echo '<div class="info">';
            echo '<strong>Session Info:</strong><br>';
            echo 'Session ID: ' . session_id() . '<br>';
            echo 'Session Name: ' . session_name() . '<br>';
            echo 'Session Save Path: ' . session_save_path() . '<br>';
            echo '</div>';
        } else {
            echo '<div class="error">❌ Cannot start PHP session</div>';
        }

        // Test 6: File Permissions
        echo '<h2>Test 6: File Permissions</h2>';

        $writablePath = dirname(__DIR__) . '/writable/session';

        if (is_dir($writablePath)) {
            echo '<div class="success">✅ Session directory exists: <code>' . $writablePath . '</code></div>';

            if (is_writable($writablePath)) {
                echo '<div class="success">✅ Session directory is writable</div>';
            } else {
                echo '<div class="error">';
                echo '❌ Session directory is NOT writable<br>';
                echo 'Run: <code>chmod -R 777 ' . dirname(__DIR__) . '/writable/</code>';
                echo '</div>';
            }
        } else {
            echo '<div class="error">❌ Session directory does not exist: ' . $writablePath . '</div>';
        }

        $mysqli->close();
        ?>

        <h2>Summary</h2>

        <div class="credential-box">
            <h3>📋 Login Credentials</h3>
            <table>
                <tr>
                    <td><strong>Login URL:</strong></td>
                    <td><code>http://localhost:8888/Ind6TokenVendor/auth/login</code></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><code>admin@ind6token.com</code></td>
                </tr>
                <tr>
                    <td><strong>Password:</strong></td>
                    <td><code>password123</code></td>
                </tr>
            </table>
        </div>

        <div class="info">
            <strong>✅ If all tests above passed:</strong>
            <ol>
                <li>Click "Go to Login Page" below</li>
                <li>Enter the credentials shown above</li>
                <li>Click "Sign in"</li>
                <li>You should be redirected to the dashboard</li>
            </ol>

            <strong>❌ If login still doesn't work:</strong>
            <ol>
                <li>Clear your browser cache and cookies</li>
                <li>Check the log file: <code>writable/logs/log-<?= date('Y-m-d') ?>.php</code></li>
                <li>Look for lines containing "Login attempt" and "Password"</li>
            </ol>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <a href="http://localhost:8888/Ind6TokenVendor/auth/login" class="btn btn-success">🔐 Go to Login Page</a>
            <a href="http://localhost:8888/Ind6TokenVendor/" class="btn">🏠 Go to Dashboard</a>
            <a href="javascript:location.reload()" class="btn">🔄 Refresh Test</a>
        </div>
    </div>
</body>

</html>