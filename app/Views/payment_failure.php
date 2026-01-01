<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .failure-container {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .failure-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #f44336, #e53935);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        .failure-icon i {
            font-size: 50px;
            color: white;
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 40px;
        }

        .reason-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
        }

        .reason-box .label {
            font-weight: 600;
            color: #856404;
            margin-bottom: 8px;
        }

        .reason-box .text {
            color: #856404;
            font-size: 14px;
        }

        .details-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-size: 14px;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .amount-highlight {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .amount-highlight .label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .amount-highlight .value {
            font-size: 36px;
            font-weight: bold;
            color: #333;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
        }

        @media (max-width: 600px) {
            .failure-container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 24px;
            }

            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="failure-container">
        <div class="failure-icon">
            <i class="fas fa-times"></i>
        </div>
        
        <h1>Payment Failed</h1>
        <p class="subtitle">We couldn't process your payment</p>
        
        <div class="reason-box">
            <div class="label"><i class="fas fa-exclamation-triangle"></i> Reason</div>
            <div class="text"><?= $reason ?></div>
        </div>
        
        <div class="amount-highlight">
            <div class="label">Transaction Amount</div>
            <div class="value">₹<?= $amount ?></div>
        </div>
        
        <div class="details-box">
            <div class="detail-row">
                <span class="detail-label">Transaction ID</span>
                <span class="detail-value"><?= $transaction_id ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date & Time</span>
                <span class="detail-value"><?= $date ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value" style="color: #f44336;">
                    <i class="fas fa-times-circle"></i> <?= $status ?>
                </span>
            </div>
        </div>
        
        <div class="btn-container">
            <a href="<?= base_url('/') ?>" class="btn btn-primary">
                <i class="fas fa-redo"></i> Try Again
            </a>
            <a href="<?= base_url('/') ?>" class="btn btn-secondary">
                <i class="fas fa-home"></i> Go to Home
            </a>
        </div>
    </div>
</body>
</html>
