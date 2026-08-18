<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - T.C. Kırklareli Belediyesi</title>
   @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

@php
    $htmlContent = (string) ($page->content ?? '');
    $rawContent = trim(preg_replace('/\s+/u', ' ', strip_tags($htmlContent)) ?? '');
    // Yalnızca görsel/medya içeren sayfalar (ör. misyon-vizyon) boş sayılmasın
    $hasMedia = (bool) preg_match('/<(img|iframe|video|embed|object|figure|svg)\b/i', $htmlContent);
    $isEmptyContent = (! $hasMedia && $rawContent === '')
        || str_contains(mb_strtolower($rawContent), 'içerik henüz yüklenemedi')
        || str_contains(mb_strtolower($rawContent), 'liste yüklenemedi');
@endphp

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container mb-5">

        <div class="page-container">
            <h1 class="page-title">{{ $page->title }}</h1>

            @if($page->image_path)
                <img src="{{ asset('storage/' . $page->image_path) }}" alt="{{ $page->title }}" class="page-featured-image">
            @endif

            <div class="page-content">
                @if($isEmptyContent)
                    <div class="alert alert-light border p-4">
                        <p class="mb-3">Bu sayfanın içeriği henüz yayınlanmamıştır. Aşağıdaki bağlantılardan ilgili bilgilere ulaşabilirsiniz.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('baskan') }}" class="btn btn-sm btn-outline-primary">Başkan</a>
                            <a href="{{ route('meclis') }}" class="btn btn-sm btn-outline-primary">Meclis Üyeleri</a>
                            <a href="{{ route('mudurler') }}" class="btn btn-sm btn-outline-primary">Organizasyon Şeması</a>
                            <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-primary">Duyurular</a>
                            <a href="{{ route('iletisim') }}" class="btn btn-sm btn-primary">İletişim</a>
                        </div>
                    </div>
                @else
                    {!! $page->content !!}
                @endif
            </div>
        </div>
    </div>
</main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

@include('layouts.footer')
</html>
