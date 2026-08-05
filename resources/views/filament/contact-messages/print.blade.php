<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>İletişim Mesajı #{{ $message->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Georgia, 'Times New Roman', serif;
            color: #1a1a1a;
            line-height: 1.55;
            margin: 0;
            padding: 32px;
            background: #fff;
        }
        .toolbar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .toolbar button {
            font-family: system-ui, sans-serif;
            font-size: 14px;
            padding: 10px 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #1a3c6e;
            color: #fff;
            cursor: pointer;
        }
        .toolbar button.secondary {
            background: #fff;
            color: #1a1a1a;
        }
        h1 {
            font-size: 22px;
            margin: 0 0 4px;
            color: #1a3c6e;
        }
        .meta {
            color: #666;
            font-size: 13px;
            font-family: system-ui, sans-serif;
            margin-bottom: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-family: system-ui, sans-serif;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
        }
        th {
            width: 160px;
            background: #f5f7fa;
            font-weight: 600;
        }
        .message-box {
            border: 1px solid #ddd;
            padding: 16px;
            white-space: pre-wrap;
            word-break: break-word;
            min-height: 120px;
            font-size: 15px;
        }
        h2 {
            font-size: 16px;
            margin: 0 0 10px;
            color: #1a3c6e;
            font-family: system-ui, sans-serif;
        }
        @media print {
            .toolbar { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Yazdır / Çıktı Al</button>
        <button type="button" class="secondary" onclick="window.close()">Kapat</button>
    </div>

    <h1>Kırklareli Belediyesi — İletişim Mesajı</h1>
    <p class="meta">Kayıt no: #{{ $message->id }} · Yazdırma: {{ now()->format('d.m.Y H:i') }}</p>

    <table>
        <tr>
            <th>Tarih</th>
            <td>{{ $message->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
        </tr>
        <tr>
            <th>Kaynak</th>
            <td>{{ $sourceLabel }}</td>
        </tr>
        <tr>
            <th>Platform</th>
            <td>{{ $message->platform ?: '—' }}</td>
        </tr>
        <tr>
            <th>Ad Soyad</th>
            <td>{{ $payload['name'] ?? '—' }}</td>
        </tr>
        <tr>
            <th>Telefon</th>
            <td>{{ $payload['phone'] ?? '—' }}</td>
        </tr>
        <tr>
            <th>E-posta</th>
            <td>{{ $payload['email'] ?? '—' }}</td>
        </tr>
        <tr>
            <th>Konu</th>
            <td>{{ $payload['subject'] ?? '—' }}</td>
        </tr>
        @if(! empty($message->ip_address))
            <tr>
                <th>IP Adresi</th>
                <td>{{ $message->ip_address }}</td>
            </tr>
        @endif
    </table>

    <h2>Mesaj İçeriği</h2>
    <div class="message-box">{{ $payload['message'] ?? '—' }}</div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 250);
        });
    </script>
</body>
</html>
