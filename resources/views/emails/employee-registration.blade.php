<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Employee Account Access - {{ $companyName }}</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }

        .content {
            padding: 20px 0;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #777777;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px 0;
        }

        .credentials {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{ $companyName }}</h1>
        </div>

        <div class="content">
            <p>Hello {{ $employee->first_name }} {{ $employee->last_name }},</p>

            <p>Your employee account has been successfully created with <strong>{{ $companyName }}</strong>.</p>

            <div class="credentials">
                <h3 style="margin-top: 0;">Your Login Credentials</h3>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Temporary Password:</strong> {{ $password }}</p>
            </div>

            <p style="font-weight: bold; color: #dc3545;">For security reasons, please change your password after first
                login.</p>

            <a href="{{ route('login') }}" class="button">Login to Your Account</a>

            <h3>Employee Information</h3>
            <p><strong>Name:</strong> {{ $employee->first_name }} {{ $employee->middle_name ?? '' }}
                {{ $employee->last_name }}</p>
            <p><strong>Employee ID:</strong> {{ $employee->employee_id }}</p>
            @if ($employee->department)
                <p><strong>Department:</strong> {{ $employee->department }}</p>
            @endif
            @if ($employee->shift)
                <p><strong>Shift:</strong> {{ $employee->shift }}</p>
            @endif
            <p><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('m/d/Y') }}
            </p>
            @if ($employee->start_date)
                <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($employee->start_date)->format('m/d/Y') }}</p>
            @endif

            <p>If you did not expect this email or believe this is an error, please contact your company administrator
                immediately.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>
                <a href="{{ route('frontend.privacy-policy') }}" style="color: #777777; text-decoration: none;">Privacy
                    Policy</a> |
                <a href="{{ route('frontend.terms-and-conditions') }}"
                    style="color: #777777; text-decoration: none;">Terms of Service</a> |
                <a href="mailto:support@mhanam.com" style="color: #777777; text-decoration: none;">Contact Support</a>
            </p>
        </div>
    </div>
</body>

</html>
