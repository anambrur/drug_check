<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .certificate {
            margin: 50px auto;
            max-width: 800px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .subtitle {
            font-size: 22px;
            margin-bottom: 40px;
        }

        .content {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .period {
            font-size: 16px;
            margin: 40px 0;
        }

        .signature {
            margin-top: 80px;
        }
    </style>
</head>

<body>
    <div class="certificate">
        <div class="title">CERTIFICATE</div>
        <div class="subtitle">OF ENROLLMENT</div>
        <div class="subtitle">Non-DOT Substance Abuse Prevention Program</div>

        <div class="content">
            <div style="font-size: 24px; margin: 30px 0;">{{ $companyName }}</div>

            Skyros Drug Checks Inc hereby certifies that the above named Company has enrolled
            in our consortium administrated random drug/alcohol testing program.
        </div>

        <div class="period">
            This certificate is for the period starting {{ $startDate }} and ending {{ $endDate }}
        </div>

        <div class="signature">
            <div style="margin-top: 50px;">{{ $signature }}</div>
            <div style="border-top: 1px solid #000; width: 200px; margin: 0 auto;"></div>
            <div>Authorized Signature</div>
        </div>
    </div>
</body>

</html>
