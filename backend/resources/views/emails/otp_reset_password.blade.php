<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Pemulihan Kata Sandi</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #333333;
        }

        .container {
            max-width: 580px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .header {
            background: linear-gradient(135deg, #146C2E 0%, #0F5122 100%);
            color: #ffffff;
            padding: 32px 24px;
            text-align: center;
        }

        .header h1 {
            margin: 0 0 6px 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .content {
            padding: 32px 28px;
        }

        .greeting {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #1e293b;
        }

        .message {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }

        .otp-box {
            background: #f0fdf4;
            border: 2px dashed #22c55e;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }

        .otp-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #15803d;
            margin-bottom: 8px;
        }

        .otp-code {
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #146C2E;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
        }

        .warning-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 12px;
            color: #92400e;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .footer {
            background-color: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>MDT HIDAYATUS SHIBYAN</h1>
            <p>Sistem Informasi Akademik & Presensi Madrasah Diniyah Takmiliyah</p>
        </div>
        <div class="content">
            <div class="greeting">Assalamu'alaikum Wr. Wb. Ustadz {{ $namaUstadz }},</div>
            <div class="message">
                Kami menerima permintaan pengaturan ulang kata sandi akun aplikasi mobile Anda. Gunakan kode verifikasi
                OTP di bawah ini untuk mengonfirmasi perubahan kata sandi:
            </div>

            <div class="otp-box">
                <div class="otp-label">Kode Verifikasi OTP Anda</div>
                <div class="otp-code">{{ $otpCode }}</div>
            </div>

            <div class="warning-box">
                <strong>PENTING:</strong> Kode OTP ini hanya berlaku selama <strong>{{ $expiredMinutes }}
                    menit</strong>. Demi keamanan akun Anda, <strong>JANGAN PERNAH</strong> membagikan kode ini kepada
                siapa pun termasuk pengurus madrasah.
            </div>

            <div class="message" style="font-size: 12px; color: #64748b;">
                Jika Anda tidak merasa melakukan permintaan reset kata sandi, abaikan email ini. Akun dan kata sandi
                Anda akan tetap aman.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MDT Hidayatus Shibyan Somorkoneng. Hak Cipta Dilindungi Undang-Undang.<br>
            Email ini dikirim secara otomatis oleh sistem, mohon tidak membalas email ini.
        </div>
    </div>
</body>

</html>
