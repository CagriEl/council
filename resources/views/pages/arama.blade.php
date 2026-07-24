<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(strlen($q) >= 2)
            Arama — {{ \Illuminate\Support\Str::limit($q, 40) }} - T.C. Kırklareli Belediyesi
        @else
            Arama - T.C. Kırklareli Belediyesi
        @endif
    </title>
    @include('layouts.frontend-head')
    <style>
        .search-result-card {
            display: block; background: #fff; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 12px;
            border: 1px solid #eee; text-decoration: none; color: inherit; transition: box-shadow 0.2s, border-color 0.2s;
        }
        .search-result-card:hover { box-shadow: 0 6px 18px rgba(0,0,0,0.08); border-color: #3498db; }
        .search-result-card .badge-type { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; display: inline-block; }
        .search-result-card .res-title { font-weight: 700; color: #1a3c6e; margin-bottom: 6px; }
        .search-result-card .res-snippet { font-size: 0.9rem; color: #555; }
        .search-section-title { font-size: 1.15rem; font-weight: 700; color: #1a3c6e; margin: 2rem 0 1rem; }
        .search-section-title:first-of-type { margin-top: 0; }
    </style>
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
<div class="container mb-5 mt-2">
    <h1 class="page-title mb-2">Site içi arama</h1>
    <div class="title-divider mb-4"></div>

    <form action="{{ route('search') }}" method="get" class="row g-2 mb-4" role="search">
        <div class="col-md-8">
            <label for="arama-q" class="visually-hidden">Aranacak kelime</label>
            <input type="search" name="q" id="arama-q" value="{{ $q }}" class="form-control form-control-lg" placeholder="En az 2 karakter yazın..." minlength="2" maxlength="200" autocomplete="off">
        </div>
        <div class="col-md-4 d-grid d-md-block">
            <button type="submit" class="btn btn-lg btn-primary w-100 w-md-auto">Ara</button>
        </div>
    </form>

    @if(strlen($q) > 0 && strlen($q) < 2)
        <p class="text-muted">Arama yapmak için en az 2 karakter girin.</p>
    @elseif(strlen($q) >= 2)
        @if($news->isEmpty() && $announcements->isEmpty())
            <p class="text-muted">“{{ $q }}” için sonuç bulunamadı.</p>
        @else
            @if($news->isNotEmpty())
                <h2 class="search-section-title">Haberler ({{ $news->count() }})</h2>
                @foreach($news as $item)
                    <a href="{{ route('news.detail', $item->slug) }}" class="search-result-card">
                        <span class="badge-type text-danger">Haber</span>
                        <div class="res-title">{{ $item->title }}</div>
                        <div class="res-snippet">{{ \Illuminate\Support\Str::limit(strip_tags($item->summary ?: $item->content), 160) }}</div>
                    </a>
                @endforeach
            @endif

            @if($announcements->isNotEmpty())
                <h2 class="search-section-title">Duyurular ({{ $announcements->count() }})</h2>
                @foreach($announcements as $item)
                    <a href="{{ route('announcement.show', $item->slug) }}" class="search-result-card">
                        <span class="badge-type text-primary">Duyuru</span>
                        <div class="res-title">{{ $item->title }}</div>
                        <div class="res-snippet">{{ \Illuminate\Support\Str::limit(strip_tags((string) $item->content), 160) }}</div>
                    </a>
                @endforeach
            @endif
        @endif
    @else
        <p class="text-muted">Haber ve duyuru başlıkları ile içeriklerinde arama yapabilirsiniz.</p>
    @endif

</div>
</main>

@include('layouts.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
