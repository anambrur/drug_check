<!-- resources/views/emails/employee-selected.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Random Selection Notification</title>
</head>
<body>
    <h2>Random Drug Test Selection Notification</h2>
    
    <p>Dear {{ $employee->first_name }} {{ $employee->last_name }},</p>
    
    <p>You have been randomly selected for a drug test as part of our ongoing compliance program.</p>
    
    <h3>Next Steps:</h3>
    <ol>
        <li>Please report to the testing location within 24 hours</li>
        <li>Bring a valid photo ID</li>
        <li>Follow all instructions from the testing staff</li>
    </ol>
    
    <p><strong>Testing Location:</strong><br>
    {{ $protocol->testing_location ?? 'Company designated testing facility' }}
    </p>
    
    <p>If you have any questions, please contact HR at {{ $protocol->contact_phone ?? 'your HR department' }}.</p>
    
    <p>Sincerely,<br>
    The Compliance Team<br>
    {{ $protocol->client->company_name }}</p>
</body>
</html>