<?php
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: A4 landscape;
            margin: 4mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            color: #112a52;
            font-family: DejaVu Sans, sans-serif;
            background: #ffffff;
        }

        .page-wrap {
            width: 100%;
            height: 188mm;
        }

        .certificate-shell {
            position: relative;
            border: 1.2px solid #cda74a;
            padding: 8px;
            height: 100%;
        }

        .certificate-card {
            position: relative;
            border: 1.8px solid #123b76;
            padding: 14px 18px;
            background: #ffffff;
            overflow: hidden;
            height: 100%;
        }

        .watermark-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 260px;
            height: 260px;
            margin-top: -130px;
            margin-left: -130px;
            opacity: 0.08;
            z-index: 1;
        }

        .pattern-band {
            position: absolute;
            left: 0;
            right: 0;
            height: 10px;
            z-index: 2;
        }

        .pattern-top {
            top: 0;
            border-bottom: 1px solid #d8b765;
            background: #123b76;
        }

        .pattern-bottom {
            bottom: 0;
            border-top: 1px solid #d8b765;
            background: #123b76;
        }

        .content {
            position: relative;
            z-index: 3;
            height: 100%;
        }

        .content-layout {
            width: 100%;
            border-collapse: collapse;
            height: 100%;
        }

        .content-main {
            vertical-align: top;
        }

        .content-footer {
            vertical-align: bottom;
            padding-top: 2px;
        }

        .header {
            width: 100%;
            border-bottom: 1px solid #d8e0ef;
            padding-bottom: 6px;
        }

        .header td {
            vertical-align: middle;
        }

        .logo-cell {
            width: 78px;
        }

        .logo-placeholder {
            width: 56px;
            height: 56px;
            border: 1px solid #cda74a;
            border-radius: 50%;
        }

        .logo-image {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 1px solid #cda74a;
            object-fit: cover;
        }

        .header-text {
            text-align: center;
        }

        .dept-label {
            margin: 0;
            font-size: 8px;
            letter-spacing: 2.1px;
            color: #36507d;
            text-transform: uppercase;
        }

        .title {
            margin: 3px 0 2px;
            font-size: 25px;
            letter-spacing: 1.2px;
            color: #123b76;
            font-family: DejaVu Serif, serif;
            text-transform: uppercase;
        }

        .subtitle {
            margin: 0;
            font-size: 11px;
            letter-spacing: 0.9px;
            color: #b38a2c;
            text-transform: uppercase;
            font-weight: bold;
        }

        .divider {
            margin: 8px auto 0;
            width: 190px;
            border-top: 1px solid #d5b15b;
        }

        .name-block {
            text-align: center;
            margin-top: 7px;
        }

        .lead-label {
            margin: 0;
            font-size: 9px;
            letter-spacing: 1.4px;
            color: #5b6f93;
            text-transform: uppercase;
        }

        .participant-name {
            margin: 6px 0 6px;
            font-size: 27px;
            line-height: 1.1;
            color: #123b76;
            font-family: DejaVu Serif, serif;
            font-weight: bold;
            text-transform: uppercase;
        }

        .participant-name-sm {
            font-size: 23px;
        }

        .participant-name-xs {
            font-size: 20px;
        }

        .statement {
            margin: 0 auto;
            max-width: 92%;
            font-size: 12px;
            line-height: 1.35;
            color: #20365f;
        }

        .statement strong {
            color: #123b76;
        }

        .info-grid {
            width: 100%;
            margin-top: 6px;
            border-collapse: separate;
            border-spacing: 4px;
        }

        .info-item {
            width: 33.33%;
            border: 1px solid #d4dced;
            background: #f9fbff50;
            padding: 6px 7px;
            vertical-align: top;
        }

        .info-label {
            margin: 0;
            font-size: 8px;
            letter-spacing: 0.9px;
            text-transform: uppercase;
            color: #637aa5;
        }

        .info-value {
            margin: 3px 0 0;
            font-size: 10px;
            color: #123b76;
            font-weight: bold;
            word-break: break-word;
        }

        .result-block {
            margin-top: 4px;
            border: 1px solid #d7b765;
            background: #fffdf752;
            padding: 6px 8px;
        }

        .result-title {
            margin: 0 0 5px;
            font-size: 8px;
            letter-spacing: 1.3px;
            text-transform: uppercase;
            color: #876515;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
        }

        .result-table td {
            width: 50%;
            border-right: 1px solid #e4d6af3f;
            padding-right: 10px;
            vertical-align: top;
        }

        .result-table td:last-child {
            border-right: 0;
            padding-left: 10px;
            padding-right: 0;
        }

        .score,
        .classification {
            font-size: 20px;
            color: #123b76;
            font-family: DejaVu Serif, serif;
            font-weight: bold;
            margin: 2px 0 0;
        }

        .footer-wrap {
            width: 100%;
            border-top: 1px solid #d9e1ef;
            padding-top: 5px;
        }

        .footer {
            width: 100%;
            border-collapse: collapse;
        }

        .footer td {
            vertical-align: bottom;
        }

        .verify-cell {
            width: 50%;
            vertical-align: middle;
        }

        .cert-id-box {
            border: 1px solid #d7b765;
            background: #fffdf7;
            padding: 7px 9px;
        }

        .cert-id-label {
            margin: 0;
            font-size: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #7e692d;
        }

        .cert-id-value {
            margin: 3px 0 0;
            font-size: 13px;
            color: #123b76;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .issue-date {
            margin: 6px 0 0;
            font-size: 10px;
            color: #314f7d;
        }

        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: middle;
        }

        .signature-line {
            width: 150px;
            border-top: 1px solid #2a416e;
            margin: 0 auto 5px;
        }

        .signature-name {
            margin: 0;
            font-size: 10px;
            color: #123b76;
            font-weight: bold;
        }

        .signature-title {
            margin: 2px 0 0;
            font-size: 7px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #61759d;
        }       
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/jpjfit_logo.png');
        $issuedDate = $certificate->issued_at?->format('d M Y') ?? now()->format('d M Y');
        $participantNameRaw = trim((string) $participant->full_name);
        $participantName = \Illuminate\Support\Str::limit($participantNameRaw, 44, '...');
        $statementName = \Illuminate\Support\Str::limit($participantNameRaw, 60, '...');
        $participantNameLength = mb_strlen($participantNameRaw);
        $participantNameClass = 'participant-name';

        if ($participantNameLength > 42) {
            $participantNameClass = 'participant-name participant-name-xs';
        } elseif ($participantNameLength > 30) {
            $participantNameClass = 'participant-name participant-name-sm';
        }

        $participantGridName = \Illuminate\Support\Str::limit($participantNameRaw, 38, '...');
        $assessmentZone = \Illuminate\Support\Str::limit((string) $session->session_code, 28, '...');
        $assessmentLocation = \Illuminate\Support\Str::limit((string) $session->location, 34, '...');
        $classification = \Illuminate\Support\Str::limit((string) $fitnessResult->classification, 18, '...');
        $signatoryName = \Illuminate\Support\Str::limit((string) (auth()->user()->name ?? 'JPJFit System'), 26, '...');
        $certificateId = \Illuminate\Support\Str::limit((string) $certificate->certificate_no, 42, '...');
    @endphp

    <div class="page-wrap">
        <div class="certificate-shell">
            <div class="certificate-card">
                <div class="pattern-band pattern-top"></div>
                <div class="pattern-band pattern-bottom"></div>
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="JPJ watermark" class="watermark-logo">
                @endif

                <div class="content">
                    <table class="content-layout" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="content-main">
                                <table class="header" cellpadding="0" cellspacing="0" style="margin-top: 20px; margin-bottom: 20px;">
                                    <tr>
                                        <td class="logo-cell">
                                            @if(file_exists($logoPath))
                                                <img src="{{ $logoPath }}" alt="Road Transport Department Malaysia Logo" class="logo-image">
                                            @else
                                                <div class="logo-placeholder"></div>
                                            @endif
                                        </td>
                                        <td class="header-text">
                                            <p class="dept-label">Road Transport Department Malaysia</p>
                                            <h1 class="title">Certificate of Fitness</h1>
                                            <p class="subtitle">JPJFit UKJK Assessment</p>
                                            <div class="divider"></div>
                                        </td>
                                        <td class="logo-cell"></td>
                                    </tr>
                                </table>

                                <div class="name-block">
                                    <p class="lead-label">This certifies that</p>
                                    <p class="{{ $participantNameClass }}">{{ $participantName }}</p>
                                    <p class="statement">
                                        This certifies that <strong>{{ $statementName }}</strong> has successfully passed the UKJK Fitness Assessment conducted by the Road Transport Department Malaysia (JPJ).
                                    </p>
                                </div>

                                <table class="info-grid" cellpadding="0" cellspacing="0" style="margin-top: 20px; margin-bottom: 20px;">
                                    <tr>
                                        <td class="info-item">
                                            <p class="info-label">Name</p>
                                            <p class="info-value">{{ $participantGridName }}</p>
                                        </td>
                                        <td class="info-item">
                                            <p class="info-label">IC Number</p>
                                            <p class="info-value">{{ $participant->ic_no }}</p>
                                        </td>
                                        <td class="info-item">
                                            <p class="info-label">Participant Number</p>
                                            <p class="info-value">{{ $participant->participant_no }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="info-item">
                                            <p class="info-label">Assessment Zone</p>
                                            <p class="info-value">{{ $assessmentZone }}</p>
                                        </td>
                                        <td class="info-item">
                                            <p class="info-label">Location</p>
                                            <p class="info-value">{{ $assessmentLocation }}</p>
                                        </td>
                                        <td class="info-item">
                                            <p class="info-label">Date</p>
                                            <p class="info-value">{{ $session->session_date?->format('d M Y') }}</p>
                                        </td>
                                    </tr>
                                </table>

                                <div class="result-block">
                                    <p class="result-title">Result Summary</p>
                                    <table class="result-table" cellpadding="0" cellspacing="0" style="margin-top: 20px; margin-bottom: 20px;">
                                        <tr>
                                            <td>
                                                <p class="info-label">Final Score</p>
                                                <p class="score">{{ number_format((float) $fitnessResult->total_score, 1) }}</p>
                                            </td>
                                            <td>
                                                <p class="info-label">Classification</p>
                                                <p class="classification">{{ $classification }}</p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="content-footer" >
                                <div class="footer-wrap">
                                    <table class="footer" cellpadding="0" cellspacing="0" style="margin-top: 50px;">
                                        <tr>
                                            <td class="verify-cell">
                                                <div class="cert-id-box">
                                                    <p class="cert-id-label">Certificate Number / Verification ID</p>
                                                    <p class="cert-id-value">{{ $certificateId }}</p>
                                                </div>
                                                <p class="issue-date"><strong>Issue Date:</strong> {{ $issuedDate }}</p>
                                            </td>

                                            <td class="signature-cell">
                                                <div class="signature-line"></div>
                                                <p class="signature-name">{{ $signatoryName }}</p>
                                                <p class="signature-title">System Admin</p>  
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>