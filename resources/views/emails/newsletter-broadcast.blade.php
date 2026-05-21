<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0;padding:0;background:#f2f7fb;font-family:Arial,Helvetica,sans-serif;color:#122840;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:22px 10px;">
    <tr>
        <td align="center">
            <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;width:100%;background:#fff;border:1px solid #dce8f4;border-radius:14px;overflow:hidden;">
                <tr>
                    <td style="padding:18px 22px;background:linear-gradient(130deg,#0a4f8f 0%,#0f80c2 100%);color:#fff;">
                        <h1 style="margin:0;font-size:20px;line-height:1.25;">{{ $subjectLine }}</h1>
                        <p style="margin:7px 0 0;font-size:12px;opacity:0.9;">{{ $appName }} Newsletter</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 22px;">
                        <p style="margin:0 0 10px;font-size:15px;">Hello {{ $recipientName ?: 'Subscriber' }},</p>
                        <div style="font-size:14px;line-height:1.7;color:#2c4a68;">{!! nl2br(e($messageBody)) !!}</div>
                        <p style="margin:14px 0 0;font-size:12px;color:#59708d;">You are receiving this because you subscribed on {{ $appName }}.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
