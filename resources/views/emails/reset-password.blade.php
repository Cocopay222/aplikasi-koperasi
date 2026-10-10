<!DOCTYPE html>
<html>
<head>
    <title>Reset Kata Sandi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; margin: 0;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #17447e; margin-top: 0;">Halo,</h2>
        <p style="color: #333; line-height: 1.6;">
            Anda menerima email ini karena kami menerima permintaan pengaturan ulang kata sandi untuk akun Anda.
        </p>

        {{-- Tombol utama --}}
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}"
               target="_blank"
               style="background-color: #17447e; color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block; font-size: 16px; letter-spacing: 0.5px;">
                🔑 Atur Ulang Kata Sandi
            </a>
        </div>

        <p style="color: #333; line-height: 1.6;">
            Tautan pengaturan ulang kata sandi ini akan kedaluwarsa dalam <strong>7 hari</strong>.
        </p>
        <p style="color: #333; line-height: 1.6;">
            Jika Anda tidak meminta pengaturan ulang kata sandi, tidak ada tindakan lebih lanjut yang diperlukan.
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">

        {{-- Fallback: salin URL manual --}}
        <p style="color: #555; font-size: 13px; line-height: 1.6;">
            <strong>Jika tombol tidak terbuka otomatis:</strong><br>
            Salin link di bawah ini, lalu buka browser Chrome di HP Anda dan tempel di address bar:
        </p>
        <div style="background-color: #f0f4ff; border: 1px solid #c7d4f0; border-radius: 6px; padding: 12px; word-break: break-all; font-size: 13px;">
            <a href="{{ $resetUrl }}" style="color: #17447e; text-decoration: none;">{{ $resetUrl }}</a>
        </div>
    </div>
</body>
</html>
