<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talep / Şikayet - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container py-5">
        <div class="mb-4">
            <h1 class="mb-2">Talep / Şikayet ve İletişim</h1>
            <p class="text-muted mb-0">
                Talep, şikayet ve iletişim başvuruları tek form üzerinden alınır.
                Aynı alandan takip numaranızla durum sorgulayabilirsiniz.
            </p>
        </div>

        <x-contact-form source="talep-sikayet-sayfasi" :enable-tracking="true" />
    </div>
</main>

@include('layouts.footer')
</body>
</html>
