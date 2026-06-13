<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login OTP</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6fa; margin: 0; padding: 0; }
        .container { background: #fff; max-width: 480px; margin: 40px auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 32px; }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo img { max-width: 120px; }
        .title { text-align: center; font-size: 1.5em; font-weight: bold; margin-bottom: 16px; color: #222; }
        .otp-btn {
            display: inline-block;
            background: #2d8cf0;
            color: #fff;
            font-size: 1.5em;
            font-weight: bold;
            padding: 12px 32px;
            border-radius: 6px;
            margin: 24px 0;
            text-decoration: none;
            letter-spacing: 2px;
        }
        .footer { margin-top: 32px; font-size: 0.9em; color: #888; text-align: center; }
        .support { font-size: 0.95em; color: #888; margin-top: 24px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">{{ config('app.name') }}</div>
        <p>Hello,</p>
        <p>You are receiving this email because we received a login request for your account.</p>
        <div style="text-align:center;">
            <span class="otp-btn">{{ $data }}</span>
        </div>
        <p style="text-align:center;">This OTP is valid for a limited time.</p>
        <p>If you did not request this OTP, no further action is required.</p>
        <p>Regards,<br>{{ config('app.name') }}</p>
        <div class="support">
            If you’re having trouble, contact us at {{ config('mail.from.address') }}
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>