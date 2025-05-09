<!DOCTYPE html>
<html>

<head>
    <title>Account Registration</title>
</head>

<body>
    <h2>Welcome to Our Platform</h2>
    <p>Your company <strong>{{ $client->company_name }}</strong> has been successfully registered.</p>

    <h3>Your Login Credentials:</h3>
    <ul>
        <li><strong>Email:</strong> {{ $email }}</li>
        <li><strong>Temporary Password:</strong> {{ $password }}</li>
    </ul>

    <p><strong>Important:</strong> Please change your password after first login.</p>

    <h3>Company Details:</h3>
    <ul>
        <li><strong>Company Name:</strong> {{ $client->company_name }}</li>
        <li><strong>Address:</strong> {{ $client->address }}, {{ $client->city }}, {{ $client->state }}
            {{ $client->zip }}</li>
        <li><strong>Phone:</strong> {{ $client->phone }}</li>
    </ul>

    <p>Thank you,</p>
    <p>The Administration Team</p>
</body>

</html>
