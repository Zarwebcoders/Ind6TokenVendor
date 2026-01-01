<?php
/**
 * Emergency Diagnostic Tool
 * Upload this to your cPanel public_html folder
 * Access via: https://ind6vendorfinal.zarwebcoders.in/diagnose.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Diagnostic Report - Ind6TokenVendor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }

        h2 {
            color: #007bff;
            margin-top: 30px;
        }

        .success {
            color: #28a745;
            font-weight: bold;
        }

        .error {
            color: #dc3545;
            font-weight: bold;
        }

        .warning {
            color: #ffc107;
            font-weight: bold;
        }

        .info {
            background: #e7f3ff;
            padding: 10px;
            border-left: 4px solid #007bff;
            margin: 10px 0;
        }

        .error-box {
            background: #ffe7e7;
            padding: 10px;
            border-left: 4px solid #dc3545;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
        }

        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
        }

        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔍 Diagnostic Report - Ind6TokenVendor</h1>
        <p><strong>Generated:</strong>
            <?= date('Y-m-d H:i:s') ?>
        </p>

        <?php
        $errors = [];
        $warnings = [];
        $success = [];

        // 1. PHP Version Check
        echo "<h2>1. PHP Environment</h2>";
        echo "<table>";
        echo "<tr><th>Check</th><th>Value</th><th>Status</th></tr>";

        $phpVersion = phpversion();
        $phpOk = version_compare($phpVersion, '8.1', '>=');
        echo "<tr><td>PHP Version</td><td>$phpVersion</td><td>";
        if ($phpOk) {
            echo "<span class='success'>✓ OK</span>";
            $success[] = "PHP version is compatible";
        } else {
            echo "<span class='error'>✗ FAIL - Need 8.1+</span>";
            $errors[] = "PHP version must be 8.1 or higher";
        }
        echo "</td></tr>";

        // Required extensions
        $requiredExtensions = ['intl', 'mbstring', 'json', 'curl'];
        foreach ($requiredExtensions as $ext) {
            $loaded = extension_loaded($ext);
            echo "<tr><td>Extension: $ext</td><td>" . ($loaded ? 'Loaded' : 'Missing') . "</td><td>";
            if ($loaded) {
                echo "<span class='success'>✓ OK</span>";
            } else {
                echo "<span class='error'>✗ MISSING</span>";
                $errors[] = "Missing PHP extension: $ext";
            }
            echo "</td></tr>";
        }

        // Database extensions
        $dbExtensions = ['mysqli', 'mysqlnd', 'pdo_mysql'];
        $dbOk = false;
        foreach ($dbExtensions as $ext) {
            if (extension_loaded($ext)) {
                $dbOk = true;
                echo "<tr><td>Database: $ext</td><td>Loaded</td><td><span class='success'>✓ OK</span></td></tr>";
                break;
            }
        }
        if (!$dbOk) {
            echo "<tr><td>Database Extensions</td><td>None found</td><td><span class='error'>✗ FAIL</span></td></tr>";
            $errors[] = "No MySQL extension found";
        }

        echo "</table>";

        // 2. File System Check
        echo "<h2>2. File System</h2>";
        echo "<table>";
        echo "<tr><th>Path</th><th>Exists</th><th>Readable</th><th>Writable</th></tr>";

        $paths = [
            'Current Directory' => __DIR__,
            'app/' => __DIR__ . '/app',
            'app/Config/' => __DIR__ . '/app/Config',
            'app/Config/App.php' => __DIR__ . '/app/Config/App.php',
            'app/Config/Paths.php' => __DIR__ . '/app/Config/Paths.php',
            'public/' => __DIR__ . '/public',
            'public/index.php' => __DIR__ . '/public/index.php',
            'public/.htaccess' => __DIR__ . '/public/.htaccess',
            'vendor/' => __DIR__ . '/vendor',
            'vendor/autoload.php' => __DIR__ . '/vendor/autoload.php',
            'writable/' => __DIR__ . '/writable',
            '.env' => __DIR__ . '/.env',
            '.htaccess' => __DIR__ . '/.htaccess',
        ];

        foreach ($paths as $name => $path) {
            $exists = file_exists($path);
            $readable = $exists && is_readable($path);
            $writable = $exists && is_writable($path);

            echo "<tr><td>$name</td>";
            echo "<td>" . ($exists ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td>";
            echo "<td>" . ($readable ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td>";
            echo "<td>" . ($writable ? "<span class='success'>✓</span>" : "<span class='warning'>-</span>") . "</td>";
            echo "</tr>";

            if (!$exists) {
                $errors[] = "Missing: $name";
            } elseif (!$readable) {
                $errors[] = "Not readable: $name";
            }
        }

        echo "</table>";

        // 3. .htaccess Check
        echo "<h2>3. .htaccess Configuration</h2>";

        $htaccessRoot = __DIR__ . '/.htaccess';
        $htaccessPublic = __DIR__ . '/public/.htaccess';

        if (file_exists($htaccessRoot)) {
            echo "<div class='info'><strong>Root .htaccess found</strong></div>";
            echo "<pre>" . htmlspecialchars(file_get_contents($htaccessRoot)) . "</pre>";
        } else {
            echo "<div class='error-box'><strong>Root .htaccess NOT FOUND!</strong></div>";
            $errors[] = "Root .htaccess file is missing";
        }

        if (file_exists($htaccessPublic)) {
            echo "<div class='info'><strong>Public .htaccess found</strong></div>";
            echo "<pre>" . htmlspecialchars(substr(file_get_contents($htaccessPublic), 0, 500)) . "...</pre>";
        } else {
            echo "<div class='error-box'><strong>Public .htaccess NOT FOUND!</strong></div>";
            $errors[] = "Public .htaccess file is missing";
        }

        // 4. Environment File Check
        echo "<h2>4. Environment Configuration</h2>";

        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            echo "<div class='info'><strong>.env file found</strong></div>";
            $envContent = file_get_contents($envFile);

            // Check for critical settings (hide sensitive data)
            $hasBaseURL = strpos($envContent, 'app.baseURL') !== false;
            $hasDBHost = strpos($envContent, 'database.default.hostname') !== false;
            $hasDBName = strpos($envContent, 'database.default.database') !== false;

            echo "<table>";
            echo "<tr><th>Setting</th><th>Status</th></tr>";
            echo "<tr><td>app.baseURL configured</td><td>" . ($hasBaseURL ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td></tr>";
            echo "<tr><td>Database host configured</td><td>" . ($hasDBHost ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td></tr>";
            echo "<tr><td>Database name configured</td><td>" . ($hasDBName ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td></tr>";
            echo "</table>";

            if (!$hasBaseURL || !$hasDBHost || !$hasDBName) {
                $warnings[] = ".env file needs configuration";
            }
        } else {
            echo "<div class='error-box'><strong>.env file NOT FOUND!</strong></div>";
            $errors[] = ".env file is missing - copy from 'env' template";
        }

        // 5. Composer Autoload Check
        echo "<h2>5. Composer Dependencies</h2>";

        $autoloadFile = __DIR__ . '/vendor/autoload.php';
        if (file_exists($autoloadFile)) {
            echo "<div class='info'><strong>Composer autoload found</strong></div>";

            // Try to include it
            try {
                require_once $autoloadFile;
                echo "<p class='success'>✓ Autoload file loaded successfully</p>";
                $success[] = "Composer dependencies are working";
            } catch (Exception $e) {
                echo "<div class='error-box'>Error loading autoload: " . $e->getMessage() . "</div>";
                $errors[] = "Composer autoload error: " . $e->getMessage();
            }
        } else {
            echo "<div class='error-box'><strong>vendor/autoload.php NOT FOUND!</strong></div>";
            echo "<p>Run: <code>composer install --no-dev</code></p>";
            $errors[] = "Composer dependencies not installed";
        }

        // 6. CodeIgniter Bootstrap Test
        echo "<h2>6. CodeIgniter Bootstrap Test</h2>";

        $publicIndex = __DIR__ . '/public/index.php';
        if (file_exists($publicIndex)) {
            echo "<div class='info'><strong>public/index.php found</strong></div>";

            // Check if it can find Paths.php
            $pathsFile = __DIR__ . '/app/Config/Paths.php';
            if (file_exists($pathsFile)) {
                echo "<p class='success'>✓ app/Config/Paths.php found</p>";

                // Try to check the Paths class
                try {
                    require_once $pathsFile;
                    if (class_exists('Config\\Paths')) {
                        echo "<p class='success'>✓ Paths class loaded successfully</p>";
                        $success[] = "CodeIgniter Paths configuration is working";
                    } else {
                        echo "<p class='error'>✗ Paths class not found in file</p>";
                        $errors[] = "Paths class definition issue";
                    }
                } catch (Exception $e) {
                    echo "<div class='error-box'>Error loading Paths: " . $e->getMessage() . "</div>";
                    $errors[] = "Paths loading error: " . $e->getMessage();
                }
            } else {
                echo "<p class='error'>✗ app/Config/Paths.php NOT FOUND</p>";
                $errors[] = "app/Config/Paths.php is missing";
            }
        } else {
            echo "<div class='error-box'><strong>public/index.php NOT FOUND!</strong></div>";
            $errors[] = "public/index.php is missing";
        }

        // 7. Server Information
        echo "<h2>7. Server Information</h2>";
        echo "<table>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        echo "<tr><td>Server Software</td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</td></tr>";
        echo "<tr><td>Document Root</td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</td></tr>";
        echo "<tr><td>Script Filename</td><td>" . __FILE__ . "</td></tr>";
        echo "<tr><td>Current Directory</td><td>" . __DIR__ . "</td></tr>";
        echo "<tr><td>mod_rewrite</td><td>" . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? '<span class="success">Enabled</span>' : '<span class="warning">Unknown/Check with host</span>') . "</td></tr>";
        echo "</table>";

        // 8. Summary
        echo "<h2>8. Summary</h2>";

        if (empty($errors)) {
            echo "<div class='info'>";
            echo "<h3 class='success'>✓ All Critical Checks Passed!</h3>";
            echo "<p>Your environment appears to be configured correctly.</p>";
            echo "<p><strong>Next step:</strong> Try accessing your site at the root URL</p>";
            echo "</div>";
        } else {
            echo "<div class='error-box'>";
            echo "<h3 class='error'>✗ Issues Found (" . count($errors) . ")</h3>";
            echo "<ol>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ol>";
            echo "</div>";
        }

        if (!empty($warnings)) {
            echo "<div class='info'>";
            echo "<h3 class='warning'>⚠ Warnings (" . count($warnings) . ")</h3>";
            echo "<ol>";
            foreach ($warnings as $warning) {
                echo "<li>$warning</li>";
            }
            echo "</ol>";
            echo "</div>";
        }

        // 9. Recommended Actions
        echo "<h2>9. Recommended Actions</h2>";

        if (!empty($errors)) {
            echo "<div class='error-box'>";
            echo "<h3>Fix These Issues:</h3>";
            echo "<ol>";

            if (in_array("Composer dependencies not installed", $errors)) {
                echo "<li><strong>Install Composer dependencies:</strong><br><code>composer install --no-dev --optimize-autoloader</code></li>";
            }

            if (in_array(".env file is missing - copy from 'env' template", $errors)) {
                echo "<li><strong>Create .env file:</strong><br><code>cp env .env</code><br>Then edit with your database credentials</li>";
            }

            if (strpos(implode(' ', $errors), 'PHP version') !== false) {
                echo "<li><strong>Update PHP version in cPanel:</strong><br>Go to cPanel → Select PHP Version → Choose 8.1 or higher</li>";
            }

            if (strpos(implode(' ', $errors), 'Missing PHP extension') !== false) {
                echo "<li><strong>Enable missing PHP extensions in cPanel:</strong><br>Go to cPanel → Select PHP Version → Enable required extensions</li>";
            }

            echo "</ol>";
            echo "</div>";
        }

        echo "<div class='info'>";
        echo "<h3>General Steps:</h3>";
        echo "<ol>";
        echo "<li>Run deployment script: <code>bash deploy.sh</code></li>";
        echo "<li>Edit .env file with correct database credentials</li>";
        echo "<li>Ensure document root points to: <code>public_html</code> (not public_html/public)</li>";
        echo "<li>Check file permissions: <code>chmod -R 777 writable</code></li>";
        echo "<li>Clear cache: <code>rm -rf writable/cache/* writable/debugbar/*</code></li>";
        echo "</ol>";
        echo "</div>";

        ?>

        <h2>10. Quick Actions</h2>
        <div class="info">
            <p><strong>After fixing issues, delete this diagnostic file for security:</strong></p>
            <code>rm diagnose.php</code>
        </div>

    </div>
</body>

</html>