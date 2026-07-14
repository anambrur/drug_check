<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Random Consortium Enrollment Notification</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            margin: 0;
            padding: 0;
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
            background-color: #0f172a;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th, .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .details-table th {
            color: #64748b;
            font-weight: 600;
            width: 40%;
        }
        .details-table td {
            color: #0f172a;
        }
        .btn-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }
        .admin-btn {
            background-color: #2e55fa;
            color: #ffffff !important;
            padding: 14px 28px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>New Consortium Enrollment Paid</h1>
    </div>
    
    <div class="content">
        <p>A new paid enrollment submission has been registered in the system. Please inspect details in the administrative dashboard.</p>
        
        <table class="details-table">
            <tbody>
                <tr>
                    <th>Enrollment Reference:</th>
                    <td>#{{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <th>Company Name:</th>
                    <td>{{ $enrollment->company_name }} @if($enrollment->dba_name) (DBA: {{ $enrollment->dba_name }}) @endif</td>
                </tr>
                <tr>
                    <th>USDOT Number:</th>
                    <td>{{ $enrollment->dot_number }}</td>
                </tr>
                <tr>
                    <th>Contact Representative:</th>
                    <td>{{ $enrollment->first_name }} {{ $enrollment->last_name }}</td>
                </tr>
                <tr>
                    <th>Email Address:</th>
                    <td>{{ $enrollment->email }}</td>
                </tr>
                <tr>
                    <th>Phone Number:</th>
                    <td>{{ $enrollment->phone }}</td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td>{{ $enrollment->address_line_1 }} {{ $enrollment->address_line_2 }}, {{ $enrollment->city }}, {{ $enrollment->state }} {{ $enrollment->zip_code }}</td>
                </tr>
                <tr>
                    <th>Selected Plan:</th>
                    <td><strong>{{ $enrollment->selected_plan }}</strong></td>
                </tr>
                <tr>
                    <th>Registered Drivers:</th>
                    <td>{{ $enrollment->driver_count }} driver(s)</td>
                </tr>
                <tr>
                    <th>Paid Amount (USD):</th>
                    <td style="color: #2e55fa; font-weight: bold;">{{ $enrollment->formatted_amount }}</td>
                </tr>
                <tr>
                    <th>Stripe Payment Intent ID:</th>
                    <td><code>{{ $enrollment->stripe_payment_intent_id }}</code></td>
                </tr>
            </tbody>
        </table>

        <div class="btn-wrapper">
            <a href="{{ route('consortium-enrollments.show', ['id' => $enrollment->id]) }}" class="admin-btn">
                Open in Admin Panel
            </a>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} DrugCheckr. Administrative Notification.</p>
    </div>
</div>

</body>
</html>
