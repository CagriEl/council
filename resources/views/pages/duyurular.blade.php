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

        .duyurular-page-head { display: flex; align-items: center; gap: 1rem 1.25rem; flex-wrap: wrap; }
        .duyurular-page-logo {
            height: auto;
            max-height: 64px;
            width: auto;
            max-width: min(140px, 38vw);
            object-fit: contain;
            flex-shrink: 0;
        }

        .ann-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (max-width: 991px) {
            .ann-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; }
        }
        @media (max-width: 480px) {
            .ann-grid { grid-template-columns: 1fr; max-width: 380px; margin-inline: auto; }
        }

        .ann-tile {
            aspect-ratio: 1 / 1;
            display: grid;
            grid-template-rows: 1fr auto;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            border: 1px solid #e8ecf1;
            box-shadow: 0 4px 14px rgba(26, 60, 110, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .ann-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(26, 60, 110, 0.14);
            border-color: #3498db;
        }
        .ann-tile .tile-media {
            position: relative;
            min-height: 0;
            background: linear-gradient(160deg, #e9ecef 0%, #dee2e6 100%);
        }
        .ann-tile .tile-media img.tile-cover-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease;
        }
        .ann-tile:hover .tile-media img.tile-cover-img { transform: scale(1.04); }
        .ann-tile .tile-media-default {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #1a3c6e 0%, #15325e 42%, #0f2748 100%);
        }
        .ann-tile .tile-media-default-logo {
            width: 58%;
            max-width: 132px;
            height: auto;
            max-height: 58%;
            object-fit: contain;
            opacity: 0.98;
            filter: drop-shadow(0 6px 18px rgba(0, 0, 0, 0.35));
            transition: transform 0.45s ease;
        }
        .ann-tile:hover .tile-media-default-logo { transform: scale(1.05); }
        .ann-tile .tile-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: clamp(2rem, 8vw, 3rem);
        }
        .ann-tile .tile-body {
            padding: 10px 12px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            border-top: 1px solid #f0f2f5;
            background: #fff;
        }
        .ann-tile .type-badge {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .ann-tile .type-duyuru { color: #0052cc; }
        .ann-tile .type-resmi { color: #e67e22; }
        .ann-tile .type-ihale { color: #c0392b; }
        .ann-tile .title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1a3c6e;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1 1 auto;
            min-height: 0;
        }
        .ann-tile .meta {
            font-size: 0.72rem;
            color: #888;
            margin-top: auto;
        }
    </style>
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
<div class="container mb-5 mt-2">
    <div class="duyurular-page-head mb-2">
        <img src="{{ asset('images/logo.png') }}" alt="T.C. Kırklareli Belediyesi" class="duyurular-page-logo" loading="eager" decoding="async">
        <h1 class="page-title mb-0">Duyurular</h1>
    </div>
    <div class="title-divider mb-4"></div>
    <p class="text-muted mb-4">Genel duyurular, resmî ilanlar ve ihale duyurularına buradan ulaşabilirsiniz.</p>

    <div class="duyuru-filter">
        <a href="{{ route('announcements.index') }}" class="{{ $currentTip === null ? 'active' : '' }}">Tümü</a>
        <a href="{{ route('announcements.index', ['tip' => 'duyuru']) }}" class="{{ $currentTip === 'duyuru' ? 'active' : '' }}">Genel duyurular</a>
        <a href="{{ route('announcements.index', ['tip' => 'resmi']) }}" class="{{ $currentTip === 'resmi' ? 'active' : '' }}">Resmî ilanlar</a>
        <a href="{{ route('announcements.index', ['tip' => 'ihale']) }}" class="{{ $currentTip === 'ihale' ? 'active' : '' }}">İhale duyuruları</a>
    </div>

    @if($announcements->isNotEmpty())
        <div class="ann-grid">
            @foreach($announcements as $item)
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
                <a href="{{ route('announcement.show', $item->slug) }}" class="ann-tile">
                    <div class="tile-media">
                        @if(filled($item->image_path))
                            <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title }}" class="tile-cover-img" loading="lazy">
                        @else
                            <div class="tile-media-default" aria-hidden="true">
                                <img src="{{ asset('images/logo.png') }}" alt="" class="tile-media-default-logo" loading="lazy" decoding="async">
                            </div>
                        @endif
                    </div>
                    <div class="tile-body">
                        <div class="type-badge {{ $typeClass }}">{{ $typeLabel }}</div>
                        <div class="title">{{ $item->title }}</div>
                        <div class="meta"><i class="far fa-calendar-alt me-1"></i>{{ $item->date->format('d.m.Y') }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="alert alert-light border text-muted">Bu kriterlere uygun duyuru bulunmamaktadır.</div>
    @endif

    <div class="mt-4 d-flex justify-content-center">
        {{ $announcements->links('pagination::bootstrap-5') }}
    </div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')
