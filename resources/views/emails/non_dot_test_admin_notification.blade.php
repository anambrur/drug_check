<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Non-DOT Test Application</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #334155; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        .header { background-color: #0f172a; color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .content { padding: 30px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .details-table th, .details-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .details-table th { color: #64748b; font-weight: 600; width: 40%; }
        .details-table td { color: #0f172a; }
        .btn-wrapper { text-align: center; margin-bottom: 20px; }
        .admin-btn { background-color: #2e55fa; color: #ffffff !important; padding: 14px 28px; border-radius: 30px; text-decoration: none; font-weight: bold; display: inline-block; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>New Non-DOT Test Application Paid</h1>
    </div>
    <div class="content">
        <p>A new paid Non-DOT test application has been registered. Please review the details in the admin dashboard.</p>
        <table class="details-table">
            <tbody>
                <tr>
                    <th>Application Reference:</th>
                    <td>#{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <th>Test Name:</th>
                    <td>{{ $portfolio->title ?? 'Non-DOT Test' }}</td>
                </tr>
                <tr>
                    <th>Applicant:</th>
                    <td>{{ $application->first_name }} {{ $application->last_name }}</td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td>{{ $application->email }}</td>
                </tr>
                <tr>
                    <th>Phone:</th>
                    <td>{{ $application->phone ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Company:</th>
                    <td>{{ $application->company_name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Reason for Testing:</th>
                    <td>{{ $application->reason_for_testing ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Preferred Location:</th>
                    <td>{{ $application->preferred_location ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Amount Paid:</th>
                    <td>{{ $application->formatted_amount }}</td>
                </tr>
            </tbody>
        </table>
        <div class="btn-wrapper">
            <a href="{{ route('admin.orders.applications.show', $application->id) }}" class="admin-btn">View Application</a>
        </div>
    </div>
    <div class="footer">
        This is an automated notification from {{ config('mail.from.name', 'DrugCheckr') }}.
    </div>
</div>
</body>
</html>
