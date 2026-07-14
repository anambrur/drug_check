<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Random Consortium Enrollment Confirmed</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #2e55fa;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .status-box {
            background-color: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .details-table th, .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .details-table th {
            color: #64748b;
            font-weight: 600;
        }
        .details-table td {
            color: #0f172a;
        }
        .receipt-total {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .next-steps {
            background-color: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
        }
        .next-steps h3 {
            margin-top: 0;
            color: #0369a1;
            font-size: 16px;
        }
        .next-steps ol {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Consortium Enrollment Confirmed</h1>
    </div>
    
    <div class="content">
        <p class="greeting">Hello {{ $enrollment->first_name }} {{ $enrollment->last_name }},</p>
        
        <p>This email confirms that your company <strong>{{ $enrollment->company_name }}</strong> has been successfully enrolled in the Random Consortium.</p>
        
        <div class="status-box">
            Payment Status: Paid & Completed
        </div>

        <div class="next-steps">
            <h3><i class="fa fa-info-circle"></i> What happens next?</h3>
            <ol>
                <li>We will review your submission details.</li>
                <li>Your official DOT driver enrollment certificate will be prepared.</li>
                <li>We will email your official certificate credentials within 24 business hours.</li>
            </ol>
        </div>

        <h3>Itemized Payment Details</h3>
        <table class="details-table">
            <tbody>
                <tr>
                    <th>Enrollment Reference:</th>
                    <td style="text-align: right;">#{{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <th>Selected Plan:</th>
                    <td style="text-align: right;">{{ $enrollment->selected_plan }}</td>
                </tr>
                <tr>
                    <th>Registered Drivers:</th>
                    <td style="text-align: right;">{{ $enrollment->driver_count }} driver(s)</td>
                </tr>
                
                @if ($pricing && $pricing->fees)
                    @foreach ($pricing->fees as $fee)
                        <tr>
                            <th>{{ $fee->fee_label }} @if($fee->fee_type == 'per_driver') (x{{ $enrollment->driver_count }}) @endif:</th>
                            <td style="text-align: right;">
                                @if($fee->fee_type == 'per_driver')
                                    ${{ number_format(($fee->fee_amount_in_dollars * $enrollment->driver_count), 2) }}
                                @else
                                    ${{ number_format($fee->fee_amount_in_dollars, 2) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr class="receipt-total">
                    <th>Total Paid (USD):</th>
                    <td style="text-align: right; color: #2e55fa;">{{ $enrollment->formatted_amount }}</td>
                </tr>
            </tbody>
        </table>

        <p>If you have any questions or require immediate support, please reply directly to this email.</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} DrugCheckr. All rights reserved.</p>
        <p>This is a secure system notification.</p>
    </div>
</div>

</body>
</html>
