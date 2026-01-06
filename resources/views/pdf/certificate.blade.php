<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            width: 29.7cm; /* A4 landscape width */
            height: 21cm;  /* A4 landscape height */
            margin: 0;
            padding: 0;
            position: relative;
        }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            background: #fff;
            overflow: hidden;
        }

        @if($hasTemplate)
        .certificate-bg {
            position: absolute;
            inset: 0;
            background-image: url("{{ $templatePath }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 1;
        }
        @else
        .certificate-bg {
            display: none;
        }
        @endif

        .certificate-content {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 2cm;
            z-index: 2;
        }

        .certificate-title {
            text-align: center;
            font-size: 45px;
            margin-top: 40px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 15px;
            font-weight: 1000;
        }

        .certificate-subtitle {
            text-align: center;
            font-size: 24px;
            margin: 10px 0;
            color: #333;
            font-weight: 1000;
            text-transform: uppercase;
            letter-spacing: 10px;
        }

        .program-name {
            text-align: center;
            font-size: 18px;
            color: #333;
            font-weight: 300;
            letter-spacing: 1px;
            line-height: 1.4;
            margin-top: 20px;
        }

        .company-name-container {
            text-align: center;
            padding: 40px 0;
        }

        .company-name {
            font-size: 32px;
            font-weight: 500;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .certificate-body {
            text-align: center;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 auto;
            max-width: 700px;
            color: #333;
            padding: 20px 0;
        }

        .certificate-period {
            text-align: center;
            font-size: 16px;
            margin: 30px 0;
            color: #333;
        }

        .signature-section {
            position: absolute;
            bottom: 15%;
            left: 15%;
            text-align: center;
        }

        .signature-name {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto 5px;
        }

        .signature-label {
            font-size: 14px;
            color: #333;
            text-transform: uppercase;
        }

        .logo-section {
            position: absolute;
            bottom: 15%;
            right: 15%;
            width: 200px;
            text-align: center;
        }

        .logo-img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        .certificate-number {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #666666;
        }

        /* Fallback border if no template */
        .fallback-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid #000000;
            z-index: 0;
        }

        /* For print/PDF specific adjustments */
        @media print {
            body {
                width: 100%;
                height: 100%;
            }
            
            .certificate-container {
                page-break-inside: avoid;
                page-break-after: avoid;
                page-break-before: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <!-- Certificate Background -->
        @if($hasTemplate)
            <div class="certificate-bg"></div>
        @else
            <div class="fallback-border"></div>
        @endif

        <!-- Certificate Content -->
        <div class="certificate-content">
            <!-- Main Title -->
            <div class="certificate-title">CERTIFICATE</div>

            <!-- Subtitle -->
            <div class="certificate-subtitle">OF ENROLLMENT</div>

            <!-- Program Name -->
            <div class="program-name">
                {{ $programLine1 }}<br/>
                {{ $programLine2 }}
            </div>

            <!-- Company Name Section -->
            <div class="company-name-container">
                <div class="company-name">{{ $companyName }}</div>
            </div>

            <!-- Certificate Body -->
            <div class="certificate-body">
                {{ $certificateBody }}
            </div>

            <!-- Certificate Period -->
            <div class="certificate-period">
                Valid from <strong>{{ $startDate }}</strong> to <strong>{{ $endDate }}</strong>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-name">Authorized Signature</div>
                <div class="signature-line"></div>
                <div class="signature-label">{{ $signatureName }}</div>
            </div>

            <!-- Logo Section -->
            @if($hasLogo)
            <div class="logo-section">
                <img src="{{ $logoPath }}" alt="Company Logo" class="logo-img">
            </div>
            @endif

            <!-- Certificate Number -->
            <div class="certificate-number">
                Certificate No: {{ $certificateNumber }} | Issued: {{ $issueDate }}
            </div>
        </div>
    </div>
</body>
</html>