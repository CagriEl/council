<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyurular - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
    <style>
        .duyuru-filter { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 2rem; }
        .duyuru-filter a {
            padding: 10px 20px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 0.9rem;
            border: 1px solid #dee2e6; color: #1a3c6e; background: #fff; transition: all 0.2s;
        }
        .duyuru-filter a:hover { border-color: #1a3c6e; background: #f8f9fa; }
        .duyuru-filter a.active { background: linear-gradient(90deg, #0052cc, #00c6ff); color: #fff; border-color: transparent; }
        .ann-list-card {
            display: flex; background: #fff; border-radius: 12px; overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; min-height: 125px;
            text-decoration: none; color: inherit; border: 1px solid #eee; transition: all 0.3s;
        }
        .ann-list-card:hover { transform: translateX(4px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); border-color: #3498db; }
        .ann-list-card .img-wrap { width: 160px; flex-shrink: 0; overflow: hidden; background: #e9ecef; }
        .ann-list-card .img-wrap img { width: 100%; height: 100%; min-height: 125px; object-fit: cover; transition: transform 0.5s; }
        .ann-list-card:hover .img-wrap img { transform: scale(1.05); }
        .ann-list-card .body { padding: 16px 18px; display: flex; flex-direction: column; justify-content: center; flex: 1; }
        .ann-list-card .type-badge { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; }
        .ann-list-card .type-duyuru { color: #0052cc; }
        .ann-list-card .type-resmi { color: #e67e22; }
        .ann-list-card .type-ihale { color: #c0392b; }
        .ann-list-card .title { font-size: 0.98rem; font-weight: 700; color: #1a3c6e; line-height: 1.35; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .ann-list-card .meta { font-size: 0.8rem; color: #888; margin-top: auto; }
        .ann-placeholder { display: flex; align-items: center; justify-content: center; color: #adb5bd; font-size: 2rem; min-height: 125px; }
        @media (max-width: 576px) {
            .ann-list-card { flex-direction: column; }
            .ann-list-card .img-wrap { width: 100%; height: 160px; }
        }
    </style>
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
<div class="container mb-5 mt-2">
    <h1 class="page-title mb-2">Duyurular</h1>
    <div class="title-divider mb-4"></div>
    <p class="text-muted mb-4">Genel duyurular, resmî ilanlar ve ihale duyurularına buradan ulaşabilirsiniz.</p>

    <div class="duyuru-filter">
        <a href="{{ route('announcements.index') }}" class="{{ $currentTip === null ? 'active' : '' }}">Tümü</a>
        <a href="{{ route('announcements.index', ['tip' => 'duyuru']) }}" class="{{ $currentTip === 'duyuru' ? 'active' : '' }}">Genel duyurular</a>
        <a href="{{ route('announcements.index', ['tip' => 'resmi']) }}" class="{{ $currentTip === 'resmi' ? 'active' : '' }}">Resmî ilanlar</a>
        <a href="{{ route('announcements.index', ['tip' => 'ihale']) }}" class="{{ $currentTip === 'ihale' ? 'active' : '' }}">İhale duyuruları</a>
    </div>

    @forelse($announcements as $item)
        @php
            $typeLabel = match ($item->type) {
                'duyuru' => 'Genel duyuru',
                'resmi' => 'Resmî ilan',
                'ihale' => 'İhale',
                default => 'Duyuru',
            };
            $typeClass = match ($item->type) {
                'duyuru' => 'type-duyuru',
                'resmi' => 'type-resmi',
                'ihale' => 'type-ihale',
                default => 'type-duyuru',
            };
        @endphp
        <a href="{{ route('announcement.show', $item->slug) }}" class="ann-list-card">
            <div class="img-wrap">
                @if($item->image_path)
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="">
                @else
                    <div class="ann-placeholder"><i class="fas fa-bullhorn"></i></div>
                @endif
            </div>
            <div class="body">
                <div class="type-badge {{ $typeClass }}">{{ $typeLabel }}</div>
                <div class="title">{{ $item->title }}</div>
                <div class="meta"><i class="far fa-calendar-alt me-1"></i>{{ $item->date->format('d.m.Y') }}</div>
            </div>
        </a>
    @empty
        <div class="alert alert-light border text-muted">Bu kriterlere uygun duyuru bulunmamaktadır.</div>
    @endforelse

    <div class="mt-4 d-flex justify-content-center">
        {{ $announcements->links('pagination::bootstrap-5') }}
    </div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')
