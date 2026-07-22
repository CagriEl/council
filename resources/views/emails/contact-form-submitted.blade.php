<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İletişim Formu</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #1a1a1a; line-height: 1.5;">
    <h2 style="color: #1a3c6e; margin-bottom: 8px;">Yeni iletişim formu mesajı</h2>
    <p style="margin-top: 0; color: #555;">Aşağıdaki mesaj web / mobil iletişim formundan iletilmiştir.</p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 640px;">
        <tr>
            <td style="border: 1px solid #ddd; background: #f7f9fc; width: 160px;"><strong>Platform</strong></td>
            <td style="border: 1px solid #ddd;">{{ $platform }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; background: #f7f9fc;"><strong>Kaynak</strong></td>
            <td style="border: 1px solid #ddd;">{{ $source }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; background: #f7f9fc;"><strong>Ad Soyad</strong></td>
            <td style="border: 1px solid #ddd;">{{ $name }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; background: #f7f9fc;"><strong>Telefon</strong></td>
            <td style="border: 1px solid #ddd;">{{ $phone ?: '—' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; background: #f7f9fc;"><strong>E-posta</strong></td>
            <td style="border: 1px solid #ddd;">{{ $email ?: '—' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; background: #f7f9fc;"><strong>Konu</strong></td>
            <td style="border: 1px solid #ddd;">{{ $subjectLine ?: '—' }}</td>
        </tr>
        @if($ipAddress)
            <tr>
                <td style="border: 1px solid #ddd; background: #f7f9fc;"><strong>IP</strong></td>
                <td style="border: 1px solid #ddd;">{{ $ipAddress }}</td>
            </tr>
        @endif
    </table>

    <h3 style="color: #1a3c6e; margin-top: 24px;">İçerik</h3>
    <div style="border: 1px solid #ddd; padding: 16px; background: #fff; white-space: pre-wrap;">{{ $messageBody }}</div>

    @if($photoUrl)
        <p style="margin-top: 16px;">
            <strong>Ek fotoğraf:</strong>
            <a href="{{ $photoUrl }}">{{ $photoUrl }}</a>
        </p>
    @endif
</body>
</html>
