<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arama@if(\Illuminate\Support\Str::length($q) >= 2) — {{ \Illuminate\Support\Str::limit($q, 40) }}@endif - T.C. Kırklareli Belediyesi</title>
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

    @if(\Illuminate\Support\Str::length($q) > 0 && \Illuminate\Support\Str::length($q) < 2)
        <p class="text-muted">Arama yapmak için en az 2 karakter girin.</p>
    @elseif(\Illuminate\Support\Str::length($q) >= 2)
        @if($news->isEmpty() && $announcements->isEmpty() && $pages->isEmpty() && $councilDecisions->isEmpty() && $directorates->isEmpty())
            <p class="text-muted">“{{ $q }}” için sonuç bulunamadı.</p>
        @else
            @if($news->isNotEmpty())
                <h2 class="search-section-title">Haberler ({{ $news->count() }})</h2>
                @foreach($news as $item)
                    <a href="{{ route('news.detail', $item->slug) }}" class="search-result-card">
                        <span class="badge-type text-danger">Haber</span>
                        <div class="res-title">{{ $item->title }}</div>
                        <div class="res-snippet">{{ Str::limit(strip_tags($item->summary ?: $item->content), 160) }}</div>
                    </a>
                @endforeach
            @endif

            @if($announcements->isNotEmpty())
                <h2 class="search-section-title">Duyurular ({{ $announcements->count() }})</h2>
                @foreach($announcements as $item)
                    <a href="{{ route('announcement.show', $item->slug) }}" class="search-result-card">
                        <span class="badge-type text-primary">Duyuru</span>
                        <div class="res-title">{{ $item->title }}</div>
                        <div class="res-snippet">{{ Str::limit(strip_tags((string) $item->content), 160) }}</div>
                    </a>
                @endforeach
            @endif

            @if($pages->isNotEmpty())
                <h2 class="search-section-title">Sayfalar ({{ $pages->count() }})</h2>
                @foreach($pages as $item)
                    <a href="{{ route('page.detail', $item->slug) }}" class="search-result-card">
                        <span class="badge-type text-success">Sayfa</span>
                        <div class="res-title">{{ $item->title }}</div>
                        <div class="res-snippet">{{ Str::limit(strip_tags((string) $item->content), 160) }}</div>
                    </a>
                @endforeach
            @endif

            @if($councilDecisions->isNotEmpty())
                <h2 class="search-section-title">Meclis Kararları ({{ $councilDecisions->count() }})</h2>
                @foreach($councilDecisions as $item)
                    <a href="{{ route('meclis-kararlari') }}" class="search-result-card">
                        <span class="badge-type text-warning">Meclis Kararı</span>
                        <div class="res-title">{{ $item->title }}</div>
                        <div class="res-snippet">
                            {{ $item->year }} {{ $item->month }} @if($item->meeting_date) • {{ $item->meeting_date->format('d.m.Y') }} @endif
                        </div>
                    </a>
                @endforeach
            @endif

            @if($directorates->isNotEmpty())
                <h2 class="search-section-title">Müdürlükler ({{ $directorates->count() }})</h2>
                @foreach($directorates as $item)
                    <a href="{{ route('mudurluk.detay', $item->slug) }}" class="search-result-card">
                        <span class="badge-type text-info">Müdürlük</span>
                        <div class="res-title">{{ $item->name }}</div>
                        <div class="res-snippet">{{ Str::limit(strip_tags((string) $item->description), 160) }}</div>
                    </a>
                @endforeach
            @endif
        @endif
    @else
        <p class="text-muted">Haber, duyuru, sayfa, meclis kararları ve müdürlük içeriklerinde arama yapabilirsiniz.</p>
    @endif

</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')
