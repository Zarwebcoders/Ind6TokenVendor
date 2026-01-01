<!DOCTYPE html>
<html>

<head>
    <title>MAMP Diagnostic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .box {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .success {
            border-left: 4px solid #4caf50;
        }

        .error {
            border-left: 4px solid #f44336;
        }

        .info {
            border-left: 4px solid #2196f3;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #666;
            margin-top: 0;
        }

        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }

        .link-box {
            background: #e3f2fd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .link-box a {
            color: #1976d2;
            text-decoration: none;
            font-weight: bold;
        }

        .link-box a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h1>🔍 MAMP Diagnostic Page</h1>

    <div class="box success">
        <h2>✅ PHP is Working!</h2>
        <p><strong>PHP Version:</strong>
            <?php echo phpversion(); ?>
        </p>
        <p><strong>Server Software:</strong>
            <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
        </p>
        <p><strong>Server Port:</strong>
            <?php echo $_SERVER['SERVER_PORT']; ?>
        </p>
    </div>

    <div class="box info">
        <h2>📂 Path Information</h2>
        <p><strong>Document Root:</strong><br><code><?php echo $_SERVER['DOCUMENT_ROOT']; ?></code></p>
        <p><strong>Script Filename:</strong><br><code><?php echo $_SERVER['SCRIPT_FILENAME']; ?></code></p>
        <p><strong>Current Directory:</strong><br><code><?php echo __DIR__; ?></code></p>
        <p><strong>Request URI:</strong><br><code><?php echo $_SERVER['REQUEST_URI']; ?></code></p>
    </div>

    <div class="box info">
        <h2>🔗 File Checks</h2>
        <?php
        $publicIndex = __DIR__ . '/public/index.php';
        $appPath = __DIR__ . '/app';
        $writable = __DIR__ . '/writable';
        ?>
        <p><strong>Public index.php:</strong>
            <?php echo file_exists($publicIndex) ? '✅ Found' : '❌ Not Found'; ?>
            <br><code><?php echo $publicIndex; ?></code>
        </p>
        <p><strong>App folder:</strong>
            <?php echo is_dir($appPath) ? '✅ Found' : '❌ Not Found'; ?>
            <br><code><?php echo $appPath; ?></code>
        </p>
        <p><strong>Writable folder:</strong>
            <?php echo is_dir($writable) ? '✅ Found' : '❌ Not Found'; ?>
            <?php if (is_dir($writable)) {
                echo is_writable($writable) ? ' (✅ Writable)' : ' (❌ Not Writable)';
            } ?>
            <br><code><?php echo $writable; ?></code>
        </p>
    </div>

    <div class="box info">
        <h2>🌐 Access URLs</h2>
        <p>Based on your current request, try these URLs:</p>

        <?php
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . '://' . $host . '/Ind6TokenVendor';
        ?>

        <div class="link-box">
            <strong>Option 1: Direct to public folder</strong><br>
            <a href="<?php echo $baseUrl; ?>/public/index.php" target="_blank">
                <?php echo $baseUrl; ?>/public/index.php
            </a>
        </div>

        <div class="link-box">
            <strong>Option 2: Via root (with redirect)</strong><br>
            <a href="<?php echo $baseUrl; ?>/index.php" target="_blank">
                <?php echo $baseUrl; ?>/index.php
            </a>
        </div>

        <div class="link-box">
            <strong>Option 3: Root folder (auto-redirect)</strong><br>
            <a href="<?php echo $baseUrl; ?>/" target="_blank">
                <?php echo $baseUrl; ?>/
            </a>
        </div>

        <div class="link-box">
            <strong>Payment Test Page:</strong><br>
            <a href="<?php echo $baseUrl; ?>/public/index.php/payment/test" target="_blank">
                <?php echo $baseUrl; ?>/public/index.php/payment/test
            </a>
        </div>

        <div class="link-box">
            <strong>MAMP Test Page:</strong><br>
            <a href="<?php echo $baseUrl; ?>/public/mamp-test.php" target="_blank">
                <?php echo $baseUrl; ?>/public/mamp-test.php
            </a>
        </div>
    </div>

    <div class="box <?php echo function_exists('apache_get_modules') ? 'success' : 'error'; ?>">
        <h2>🔧 Apache Modules</h2>
        <?php if (function_exists('apache_get_modules')): ?>
            <?php $modules = apache_get_modules(); ?>
            <p><strong>mod_rewrite:</strong>
                <?php echo in_array('mod_rewrite', $modules) ? '✅ Enabled' : '❌ Disabled'; ?>
            </p>
            <details>
                <summary>View all modules (
                    <?php echo count($modules); ?>)
                </summary>
                <ul>
                    <?php foreach ($modules as $module): ?>
                        <li>
                            <?php echo $module; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </details>
        <?php else: ?>
            <p>❌ Cannot check Apache modules (function not available)</p>
        <?php endif; ?>
    </div>

    <div class="box info">
        <h2>💡 Troubleshooting Tips</h2>
        <ol>
            <li>Make sure MAMP is running (both Apache and MySQL should be green)</li>
            <li>Verify the port is 8888 in MAMP preferences</li>
            <li>Try accessing the "Direct to public folder" link above</li>
            <li>If mod_rewrite is disabled, you'll need to use <code>index.php</code> in URLs</li>
            <li>Check MAMP logs at: <code>/Applications/MAMP/logs/</code></li>
        </ol>
    </div>

    <div class="box info" style="background: #fff3cd; border-left-color: #ffc107;">
        <h2>⚙️ Current Configuration</h2>
        <p>Based on your app configuration:</p>
        <ul>
            <li><strong>Base URL:</strong> http://localhost:8888/Ind6TokenVendor/</li>
            <li><strong>Index Page:</strong> index.php</li>
            <li><strong>Environment:</strong>
                <?php echo getenv('CI_ENVIRONMENT') ?: 'production'; ?>
            </li>
        </ul>
    </div>
</body>

</html>