<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Kode Verifikasi Dapur Cerdas</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f0f6f0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            padding: 32px 16px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            text-align: center;
        }
        .logo {
            font-size: 22px;
            font-weight: 800;
            color: #1B5E20;
            letter-spacing: 0.5px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #333333;
            margin-top: 24px;
        }
        .desc {
            font-size: 14px;
            color: #6B7280;
            line-height: 1.6;
            margin-top: 8px;
        }
        .otp-box {
            display: inline-block;
            background: #E8F5E9;
            border: 2px dashed #2E7D32;
            border-radius: 12px;
            padding: 16px 32px;
            margin: 24px 0 8px;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #1B5E20;
        }
        .note {
            font-size: 12.5px;
            color: #9CA3AF;
            line-height: 1.5;
            margin-top: 16px;
        }
        .footer {
            font-size: 11px;
            color: #B0B8B0;
            text-align: center;
            margin-top: 24px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">🌿 Dapur Cerdas</div>
            <div class="greeting">Halo, {{ $userName }}!</div>
            <div class="desc">
                Gunakan kode berikut untuk memverifikasi akun Anda dan
                melengkapi pendaftaran di Dapur Cerdas.
            </div>
            <div class="otp-box">{{ $otpCode }}</div>
            <div class="desc">
                Kode berlaku selama <strong>10 menit</strong>.<br>
                Jangan bagikan kode ini kepada siapa pun.
            </div>
            <div class="note">
                Jika Anda tidak melakukan pendaftaran, Anda dapat mengabaikan email ini.
            </div>
        </div>
        <div class="footer">
            © {{ date('Y') }} Dapur Cerdas. Tim nutrisi Si Kecil.<br>
            Email ini dikirim otomatis, mohon tidak membalas.
        </div>
    </div>
</body>
</html>