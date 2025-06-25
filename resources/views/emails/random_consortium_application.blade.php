<!DOCTYPE html>
<html>

<head>
    <title>New Random Consortium Application</title>
</head>

<body>
    <h2>New Random Consortium Application</h2>

    <h3>Company Information</h3>
    <p><strong>Name:</strong> {{ $company['name'] }}</p>
    <p><strong>Address:</strong> {{ $company['address'] }}, {{ $company['city'] }}, {{ $company['state'] }}
        {{ $company['zip'] }}</p>
    <p><strong>Phone:</strong> {{ $company['phone'] ?? 'N/A' }}</p>

    <h3>DER Information</h3>
    <p><strong>Name:</strong> {{ $der['name'] }}</p>
    <p><strong>Email:</strong> {{ $der['email'] }}</p>
    <p><strong>Phone:</strong> {{ $der['phone'] ?? 'N/A' }}</p>

    <h3>Certificate Information</h3>
    <p><strong>Start Date:</strong> {{ $certificate['start_date'] ?? 'N/A' }}</p>
    @if ($certificate['file_path'])
        <p><strong>Certificate File:</strong> Attached</p>
    @endif
</body>

</html>
