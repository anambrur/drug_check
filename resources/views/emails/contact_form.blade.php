<!DOCTYPE html>
<html>

<head>
    <title>New Random Consortium Application</title>
</head>

<body>
    <!-- resources/views/emails/contact_form.blade.php -->
    <h2>New Contact Form Submission</h2>

    <h3>Contact Information</h3>
    <p><strong>Name:</strong> {{ $data['contact_name'] }}</p>
    <p><strong>Email:</strong> {{ $data['contact_email'] }}</p>
    <p><strong>Phone:</strong> {{ $data['contact_phone'] }}</p>
    @if ($data['contact_subject'])
        <p><strong>Subject:</strong> {{ $data['contact_subject'] }}</p>
    @endif

    <h3>Message</h3>
    <p>{{ $data['contact_message'] }}</p>
</body>

</html>
