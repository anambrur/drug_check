<!DOCTYPE html>
<html>

<head>
    <title>Test Notification</title>
</head>

<body>
    <h2>You have new test results from Skyros Drug Checks Inc</h2>

    <p>Hello {{ $type === 'company' ? $data['company_name'] : $data['employee_name'] }},</p>

    <p>Skyros Drug Checks Inc has added new test to your portal.</p>

    @if ($type === 'company')
        <p>This is to inform you that a test has been scheduled for your employee {{ $data['employee_name'] }}.</p>
    @else
        <p>This is to inform you about your upcoming test.</p>
    @endif

    <h3>Test Details:</h3>
    <ul>
        <li><strong>Test Name:</strong> {{ $data['test_name'] }}</li>
        <li><strong>Test Method:</strong> {{ $data['test_method'] }}</li>
        <li><strong>Test Regulation:</strong> {{ $data['test_regulation'] }}</li>
        <li><strong>Reason For Test:</strong> {{ $data['reason_for_test'] }}</li>
        <li><strong>Specimen:</strong> {{ $data['specimen'] }}</li>
        <li><strong>Date:</strong> {{ $data['test_date'] }}</li>
        <li><strong>Time:</strong> {{ $data['test_time'] }}</li>
        <li><strong>Location:</strong> {{ $data['collection_location'] ?? 'Not specified' }}</li>
        {{-- <li><strong>Status:</strong> {{ ucfirst($data['status']) }}</li> --}}
    </ul>

    <p>Thank you,</p>
    <p><strong>Skyros Drug Checks Inc</strong></p>
    <p>
        <small>
            (This is an automated notification. Please do not reply to this email.)
        </small>
    </p>
</body>

</html>
