<!DOCTYPE html>
<html>

<head>
    <title>Your Quest Diagnostics QPassport</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Quest Diagnostics QPassport</h2>
        </div>

        <div class="content">
            <p>Hello <strong>{{ $firstName }} {{ $lastName }}</strong>,</p>

            <p>Your Quest Diagnostics order has been successfully created.</p>

            <p><strong>Order ID:</strong> {{ $questOrderId }}</p>

            <p>Attached is your QPassport document, which you should bring to your appointment.</p>

            <p>If you have any questions, please contact our support team.</p>
        </div>

        <div class="footer">
            <p>Thank you,<br>The Quest Diagnostics Team</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
