<!DOCTYPE html>
<html>

<head>
    <title>New Random Consortium Application</title>
</head>

<body>
    <!-- resources/views/emails/dot_application.blade.php -->
    <p>{{ $reason }}</p>
    <h2>New DOT/Non DOT Application</h2>
    <h3>Test Name: {{ $data['test_name'] ?? '' }}</h3>

    <h3>Applicant Information</h3>
    <p>Name: {{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</p>
    <p>Email: {{ $data['email'] ?? '' }}</p>
    <p>Phone: {{ $data['phone'] ?? '' }}</p>
    <p>Address: {{ $data['address'] ?? '' }}</p>

    <h3>Testing Information</h3>
    <p>Reason: {{ $data['reason_for_testing'] ?? '' }}</p>
    <p>Preferred Location: {{ $data['preferred_location'] ?? '' }}</p>
    <p>Date: {{ $data['date'] ?? '' }}</p>
</body>

</html>
