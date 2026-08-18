<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haberler - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
    <style>
        .haber-filter { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 2rem; }
        .haber-filter a {
            padding: 10px 20px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem;
            border: 1px solid #dee2e6; color: #1a3c6e; background: #fff; transition: all 0.2s;
        }
        .haber-filter a:hover { border-color: #1a3c6e; background: #f8f9fa; }
        .haber-filter a.active { background: linear-gradient(90deg, #0052cc, #00c6ff); color: #fff; border-color: transparent; }
        .haber-list-card {
            display: flex; background: #fff; border-radius: 12px; overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; min-height: 125px;
            text-decoration: none; color: inherit; border: 1px solid #eee; transition: all 0.3s;
        }
        .haber-list-card:hover { transform: translateX(4px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); border-color: #3498db; }
        .haber-list-card .img-wrap { width: 160px; flex-shrink: 0; overflow: hidden; background: #e9ecef; }
        .haber-list-card .img-wrap img { width: 100%; height: 100%; min-height: 125px; object-fit: cover; object-position: center top; transition: transform 0.5s; }
        .haber-list-card:hover .img-wrap img { transform: scale(1.05); }
        .haber-list-card .body { padding: 16px 18px; display: flex; flex-direction: column; justify-content: center; flex: 1; }
        .haber-list-card .cat-badge { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; color: #e74c3c; }
        .haber-list-card .title { font-size: 0.98rem; font-weight: 700; color: #1a3c6e; line-height: 1.35; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .haber-list-card .meta { font-size: 0.8rem; color: #888; margin-top: auto; }
        @media (max-width: 576px) {
            .haber-list-card { flex-direction: column; }
            .haber-list-card .img-wrap { width: 100%; height: 160px; }
        }
    </style>
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
<div class="container mb-5 mt-2">
    <h1 class="page-title mb-2">Haberler</h1>
    <div class="title-divider mb-4"></div>
    <p class="text-muted mb-4">Belediye, kültür-sanat, spor ve diğer alanlardaki haberlere göz atın.</p>

    <div class="haber-filter">
        <a href="{{ route('news.index') }}" class="{{ $currentCategory === null ? 'active' : '' }}">Tümü</a>
        <a href="{{ route('news.index', ['kategori' => 'belediye']) }}" class="{{ $currentCategory === 'belediye' ? 'active' : '' }}">Belediye</a>
        <a href="{{ route('news.index', ['kategori' => 'kultur']) }}" class="{{ $currentCategory === 'kultur' ? 'active' : '' }}">Kültür ve Sanat</a>
        <a href="{{ route('news.index', ['kategori' => 'spor']) }}" class="{{ $currentCategory === 'spor' ? 'active' : '' }}">Spor</a>
        <a href="{{ route('news.index', ['kategori' => 'cevre']) }}" class="{{ $currentCategory === 'cevre' ? 'active' : '' }}">Çevre ve Kent</a>
        <a href="{{ route('news.index', ['kategori' => 'sosyal']) }}" class="{{ $currentCategory === 'sosyal' ? 'active' : '' }}">Sosyal Hizmetler</a>
    </div>

    @php
        $categoryLabels = [
            'belediye' => 'Belediye',
            'kultur' => 'Kültür ve Sanat',
            'spor' => 'Spor',
            'cevre' => 'Çevre ve Kent',
            'sosyal' => 'Sosyal Hizmetler',
        ];
    @endphp

    @forelse($news as $item)
        @php $catLabel = $categoryLabels[$item->category] ?? 'Haber'; @endphp
        <a href="{{ route('news.detail', $item->slug) }}" class="haber-list-card">
            <div class="img-wrap">
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="">
                @else
                    <div class="d-flex align-items-center justify-content-center text-muted" style="min-height:125px;"><i class="fas fa-newspaper fa-2x"></i></div>
                @endif
            </div>
            <div class="body">
                <div class="cat-badge">{{ $catLabel }}</div>
                <div class="title">{{ $item->title }}</div>
                <div class="meta"><i class="far fa-calendar-alt me-1"></i>{{ $item->published_at->format('d.m.Y') }}</div>
            </div>
        </a>
    @empty
        <div class="alert alert-light border text-muted">Bu kriterlere uygun haber bulunmamaktadır.</div>
    @endforelse

    <div class="mt-4 d-flex justify-content-center">
        {{ $news->links('pagination::bootstrap-5') }}
    </div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')
