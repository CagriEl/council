<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $module }} - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container py-5">
        <div class="p-4 p-md-5 border rounded bg-light text-center">
            <h1 class="mb-3">{{ $module }}</h1>
            <p class="text-muted mb-4">
                Bu hizmetin dijital sürümü hazırlık aşamasındadır.
                Bu süreçte çözüm merkezi üzerinden destek alabilirsiniz.
            </p>
            <a class="btn btn-primary" href="{{ route('iletisim') }}">İletişime Geç</a>
        </div>
    </div>
</main>

@include('layouts.footer')
</body>
</html>
