<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAMP Test - Ind6TokenVendor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .status {
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .icon {
            font-size: 20px;
        }

        .links {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .link-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 8px 0;
        }

        .link-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .link-item a:hover {
            text-decoration: underline;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🚀 MAMP Setup Test</h1>

        <div class="status success">
            <span class="icon">✅</span>
            <div>
                <strong>PHP is working!</strong><br>
                Version:
                <?php echo phpversion(); ?>
            </div>
        </div>

        <div class="status success">
            <span class="icon">✅</span>
            <div>
                <strong>Server is running!</strong><br>
                Port:
                <?php echo $_SERVER['SERVER_PORT']; ?>
            </div>
        </div>

        <div class="status info">
            <span class="icon">ℹ️</span>
            <div>
                <strong>Document Root:</strong><br>
                <code><?php echo $_SERVER['DOCUMENT_ROOT']; ?></code>
            </div>
        </div>

        <div class="status info">
            <span class="icon">ℹ️</span>
            <div>
                <strong>Current URL:</strong><br>
                <code><?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></code>
            </div>
        </div>

        <div class="info-box">
            <p><strong>✨ Your MAMP setup is working correctly!</strong></p>
            <p style="margin-top: 10px;">Now try accessing your CodeIgniter application:</p>
        </div>

        <div class="links">
            <h3 style="margin-bottom: 15px; color: #333;">📌 Quick Links</h3>

            <div class="link-item">
                <strong>Main Application:</strong><br>
                <a href="http://localhost:8888/Ind6TokenVendor/index.php" target="_blank">
                    http://localhost:8888/Ind6TokenVendor/index.php
                </a>
            </div>

            <div class="link-item">
                <strong>Login Page:</strong><br>
                <a href="http://localhost:8888/Ind6TokenVendor/index.php/auth/login" target="_blank">
                    http://localhost:8888/Ind6TokenVendor/index.php/auth/login
                </a>
            </div>

            <div class="link-item">
                <strong>Payment Test:</strong><br>
                <a href="http://localhost:8888/Ind6TokenVendor/index.php/payment/test" target="_blank">
                    http://localhost:8888/Ind6TokenVendor/index.php/payment/test
                </a>
            </div>

            <div class="link-item">
                <strong>LocalPaisa Test:</strong><br>
                <a href="http://localhost:8888/Ind6TokenVendor/index.php/payment/test" target="_blank">
                    Select "LocalPaisa" from dropdown
                </a>
            </div>
        </div>

        <div class="info-box" style="margin-top: 20px; background: #fff3cd; border-left-color: #ffc107;">
            <p><strong>⚠️ Configuration Applied:</strong></p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li><code>baseURL</code>: http://localhost:8888/Ind6TokenVendor/</li>
                <li><code>indexPage</code>: index.php</li>
                <li><code>Port</code>: 8888</li>
            </ul>
        </div>
    </div>
</body>

</html>