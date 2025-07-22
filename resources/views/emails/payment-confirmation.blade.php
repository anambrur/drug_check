<!DOCTYPE html>
<html>

<head>
    <title>Payment Confirmation</title>
</head>

<body>
    <h2>Payment Confirmation</h2>

    <p>Dear {{ $data['first_name'] }} {{ $data['last_name'] }},</p>

    <p>Thank you for your payment. Your {{ $testName }} test has been scheduled successfully.</p>

    <h3>Payment Details:</h3>
    <ul>
        <li><strong>Amount Paid:</strong> ${{ number_format($amount, 2) }}</li>
        <li><strong>Payment Date:</strong> {{ now()->format('F j, Y') }}</li>
        <li><strong>Test Scheduled:</strong> {{ $data['date'] ?? 'To be confirmed' }}</li>
    </ul>

    <h3>Test Information:</h3>
    <ul>
        <li><strong>Test Name:</strong> {{ $testName }}</li>
        <li><strong>Reason for Testing:</strong> {{ $data['reason_for_testing'] }}</li>
        <li><strong>Preferred Location:</strong> {{ $data['preferred_location'] ?? 'To be confirmed' }}</li>
    </ul>

    <p>If you have any questions about your appointment, please don't hesitate to contact us.</p>

    <p>Best regards,<br>
        Your Company Name</p>
</body>

</html>
