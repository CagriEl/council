Yeni iletişim formu mesajı

Platform: {{ $platform }}
Kaynak: {{ $source }}
Ad Soyad: {{ $name }}
Telefon: {{ $phone ?: '—' }}
E-posta: {{ $email ?: '—' }}
Konu: {{ $subjectLine ?: '—' }}
@if($ipAddress)
IP: {{ $ipAddress }}
@endif

İçerik:
{{ $messageBody }}
@if($photoUrl)

Ek fotoğraf: {{ $photoUrl }}
@endif
