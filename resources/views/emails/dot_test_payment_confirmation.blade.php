<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DOT Payment Confirmation</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #334155; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        .header { background-color: #2e55fa; color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
        .content { padding: 30px; }
        .greeting { font-size: 18px; font-weight: 600; margin-top: 0; margin-bottom: 15px; }
        .status-box { background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; font-weight: 600; margin-bottom: 25px; text-align: center; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .details-table th, .details-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .details-table th { color: #64748b; font-weight: 600; }
        .details-table td { color: #0f172a; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>DOT Payment Confirmation</h1>
    </div>
    <div class="content">
        <p class="greeting">Dear {{ $application->first_name }} {{ $application->last_name }},</p>
        <div class="status-box">Your DOT test payment was received successfully.</div>
        <p>Thank you for your payment. Your {{ $portfolio->title ?? 'DOT' }} test has been scheduled.</p>
        <table class="details-table">
            <tbody>
                <tr>
                    <th>Amount Paid</th>
                    <td>${{ $amount }}</td>
                </tr>
                <tr>
                    <th>Payment Date</th>
                    <td>{{ now()->format('m/d/Y') }}</td>
                </tr>
                <tr>
                    <th>Test Name</th>
                    <td>{{ $portfolio->title ?? 'DOT Test' }}</td>
                </tr>
                <tr>
                    <th>Reason for Testing</th>
                    <td>{{ $application->reason_for_testing ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Reference</th>
                    <td>#{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
            </tbody>
        </table>
        <p>If you have any questions about your appointment, please contact us.</p>
    </div>
    <div class="footer">
        {{ config('mail.from.name', 'DrugCheckr') }}
    </div>
</div>
</body>
</html>
