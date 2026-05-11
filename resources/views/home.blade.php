@extends('layouts.master')

@section('title', 'Ana Sayfa - T.C. Kırklareli Belediyesi')

@push('styles')
<style>
    /* HERO BACKGROUND & VIDEO */
    .hero-section {
        position: relative; 
        min-height: 100vh;
        display: flex; 
        flex-direction: column; 
        justify-content: space-between; 
        padding-bottom: 0;
        overflow: hidden;
        background-color: #000; /* Video yüklenene kadar siyah fon */
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0; /* En altta */
    }

    .hero-background video, 
    .hero-background img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
    }

    /* Karartma Katmanı (Yazıların okunması için) */
    .hero-overlay {
        position: absolute;
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        z-index: 1;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.1) 50%, rgba(0, 0, 0, 0.6) 100%);
    }

    /* KARE BUTONLAR */
    .bottom-section { position: relative; z-index: 5; width: 100%; padding-left: 2rem; padding-right: 4rem; }
    .buttons-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 15px; width: 100%; margin-bottom: 20px; position: relative; z-index: 100; }
    .box-btn { width: 120px; height: 120px; background: rgba(255, 255, 255, 0.9); border-radius: 15px; border: 1px solid rgba(255,255,255,0.5); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: #1a3c6e; text-decoration: none; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); box-shadow: 0 10px 20px rgba(0,0,0,0.2); backdrop-filter: blur(10px); cursor: pointer; padding: 10px; }
    .box-btn i { font-size: 2.2rem; margin-bottom: 10px; background: -webkit-linear-gradient(#0052cc, #00c6ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; transition: all 0.3s; }
    .box-btn span { font-weight: 700; font-size: 0.75rem; text-transform: uppercase; line-height: 1.2; }
    .box-btn:hover { transform: translateY(-10px); background: #1a3c6e; color: white; border-color: #1a3c6e; box-shadow: 0 15px 30px rgba(0,0,0,0.4); }
    .box-btn:hover i { background: none; -webkit-text-fill-color: white; color: white; }

    /* KÜÇÜK HABER KARTI (SOL ALT - HERO SLIDER) */
    .news-container { width: 80%; height: 272px; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.5); border: 4px solid white; position: relative; z-index: 40; margin: 20px 0; }
    .news-img { width: 100%; height: 140px; object-fit: cover; }
    .news-caption { padding: 10px 12px; display: flex; flex-direction: column; height: 132px; justify-content: flex-start; }
    .news-tag { font-size: 0.7rem; color: #e74c3c; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }
    .news-text { font-size: 0.85rem; color: #2c3e50; font-weight: 700; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .read-more-link { margin-top: auto; color: #3498db; font-size: 0.8rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
    
    /* Oklar */
    .slider-arrow { position: absolute; top: 120px; transform: translateY(-50%); width: 32px; height: 32px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; box-shadow: 0 2px 5px rgba(0,0,0,0.3); border: none; z-index: 50; transition: background 0.2s; cursor: pointer; }
    .slider-arrow:hover { background: #e74c3c; color: white; }
    .prev-arrow { left: 10px; } .next-arrow { right: 10px; }

    /* BAŞKAN GÖRSELİ (SAĞ ALT) */
    .mayor-wrapper { position: absolute; bottom: 0; right: 0%; height: 300px; width: auto; z-index: 20; pointer-events: none; display: flex; align-items: flex-end; }
    .mayor-img { height: 100%; width: auto; filter: drop-shadow(-10px 5px 15px rgba(0,0,0,0.5)); object-fit: contain; pointer-events: none; }

    /* HABERLER MODÜLÜ (MANŞET + LİSTE) */
    .latest-news-section { background-color: #f8f9fa; padding: 60px 0; position: relative; z-index: 30; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-left: 15px; padding-right: 15px; }
    .section-title { font-size: 1.8rem; font-weight: 800; color: #1a3c6e; position: relative; padding-left: 20px; }
    .section-title::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); height: 30px; width: 6px; background: #e74c3c; border-radius: 3px; }
    .btn-view-all { color: #1a3c6e; font-weight: 700; text-decoration: none; border: 2px solid #1a3c6e; padding: 8px 20px; border-radius: 50px; transition: all 0.3s; }
    .btn-view-all:hover { background: #1a3c6e; color: white; }

    /* Büyük Manşet Kartı (görsel tam sığar: contain) */
    .headline-card { position: relative; border-radius: 15px; overflow: hidden; height: 450px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s; cursor: pointer; }
    .headline-card:hover { transform: translateY(-5px); }
    .headline-cover {
        width: 100%; height: 100%;
        background: linear-gradient(180deg, #eef2f7 0%, #e4eaf2 100%);
        display: flex; align-items: center; justify-content: center;
        padding: 12px;
    }
    .headline-cover img { max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; object-position: center; }
    .headline-card::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 70%; background: linear-gradient(to top, rgba(0,0,0,0.88), transparent); pointer-events: none; }
    .headline-body { position: absolute; bottom: 0; left: 0; padding: 30px; z-index: 2; color: white; width: 100%; }
    .headline-tag { background: #e74c3c; color: white; padding: 5px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 4px; display: inline-block; margin-bottom: 10px; text-transform: uppercase; }
    .headline-title { font-size: 1.6rem; font-weight: 700; margin-bottom: 10px; line-height: 1.3; }
    .headline-text { font-size: 0.95rem; opacity: 0.9; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 15px; }
    .headline-date { font-size: 0.85rem; opacity: 0.8; display: flex; align-items: center; gap: 5px; }

    /* Thumbnails (Küçük Resimler) */
    .headline-thumbs { display: flex; gap: 15px; margin-top: 15px; overflow-x: auto; padding-bottom: 10px; }
    .headline-thumb-item { flex: 0 0 120px; height: 80px; border-radius: 10px; overflow: hidden; cursor: pointer; position: relative; border: 2px solid transparent; transition: all 0.3s; background: linear-gradient(180deg, #eef2f7, #e4eaf2); display: flex; align-items: center; justify-content: center; padding: 4px; }
    .headline-thumb-item img { max-width: 100%; max-height: 100%; object-fit: contain; object-position: center; }
    .headline-thumb-item.active { border-color: #e74c3c; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3); }
    .headline-thumb-item::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); transition: background 0.3s; }
    .headline-thumb-item.active::after { background: transparent; }
    
    .main-slider-controls { position: absolute; bottom: 30px; right: 30px; z-index: 10; display: flex; gap: 10px; }
    .main-slider-controls .slider-btn { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.5); color: white; display: flex; align-items: center; justify-content: center; transition: all 0.3s; cursor: pointer; }
    .main-slider-controls .slider-btn:hover { background: white; color: #1a3c6e; }

    /* Sağ liste (Kırklareli duyuruları) */
    .kd-side-card {
        display: flex; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 16px; min-height: 108px; text-decoration: none; color: inherit; border: 2px solid #eee;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
    }
    .kd-side-card:hover { border-color: #3498db; box-shadow: 0 8px 20px rgba(0,0,0,0.08); transform: translateX(4px); color: inherit; }
    .kd-side-card.active { border-color: #e74c3c; box-shadow: 0 5px 16px rgba(231, 76, 60, 0.2); }
    .kd-side-thumb {
        width: 130px; flex-shrink: 0; background: linear-gradient(180deg, #eef2f7, #e4eaf2);
        display: flex; align-items: center; justify-content: center; padding: 8px;
    }
    .kd-side-thumb img { max-width: 100%; max-height: 100px; object-fit: contain; object-position: center; }
    .kd-side-body { padding: 12px 14px; display: flex; flex-direction: column; justify-content: center; flex: 1; min-width: 0; }
    .kd-side-title { font-size: 0.92rem; font-weight: 700; color: #1a3c6e; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 6px; }
    .kd-side-meta { font-size: 0.78rem; color: #888; margin-top: auto; }

    /* DUYURULAR BÖLÜMÜ (3 Sütun) */
    .announcements-section { background-color: #fff; padding: 60px 0; border-top: 1px solid #eee; }
    .ann-card { background: white; border-radius: 15px; padding: 0; height: 100%; border: 1px solid #eee; box-shadow: 0 5px 20px rgba(0,0,0,0.03); display: flex; flex-direction: column; transition: transform 0.3s; }
    .ann-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    .ann-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 15px; border-radius: 15px 15px 0 0; color: white; }
    .ann-header i { font-size: 1.5rem; }
    .ann-header h3 { font-size: 1.2rem; font-weight: 700; margin: 0; }
    .header-blue { background: linear-gradient(45deg, #1a3c6e, #2c5282); }
    .header-orange { background: linear-gradient(45deg, #e67e22, #d35400); }
    .header-dark { background: linear-gradient(45deg, #34495e, #2c3e50); }
    .ann-list { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
    .ann-item { padding: 15px 20px; border-bottom: 1px solid #f5f5f5; transition: background 0.2s; }
    .ann-item:last-child { border-bottom: none; }
    .ann-item:hover { background-color: #fcfcfc; }
    .ann-link {
        text-decoration: none;
        display: block;
        color: inherit;
        border-radius: 8px;
        margin: -4px -8px;
        padding: 4px 8px;
        transition: color 0.2s, background 0.2s;
    }
    .ann-link:hover { text-decoration: none; color: inherit; }
    .ann-text {
        font-size: 0.9rem;
        color: #2c3e50;
        font-weight: 600;
        line-height: 1.45;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .ann-date { font-size: 0.75rem; color: #8892a0; display: flex; align-items: center; gap: 6px; font-weight: 500; }
    .ann-item:hover .ann-text { color: #1a3c6e; }
    .ann-item:hover .ann-date { color: #5c6b7a; }
    .ann-footer { padding: 20px; border-top: 1px solid #eee; text-align: center; }
    .btn-ann-all { display: block; width: 100%; padding: 10px; border-radius: 50px; background: #f8f9fa; color: #1a3c6e; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: all 0.3s; border: 1px solid #eee; }
    .btn-ann-all:hover { background: #1a3c6e; color: white; }

    /* Mobil Uyum */
    @media (max-width: 992px) {
        .bottom-section { padding-left: 15px; padding-right: 15px; }
        .news-container { max-width: 400px; margin: 0 auto 20px auto; width: 100%; }
        .mayor-wrapper { display: none; }
        .buttons-container { gap: 10px; }
        .box-btn { width: 100px; height: 100px; font-size: 0.8rem; }
        .headline-card { height: 300px; }
        .headline-title { font-size: 1.3rem; }
        .headline-thumbs { display: none; }
        .kd-side-card { flex-direction: column; min-height: 0; }
        .kd-side-thumb { width: 100%; height: 140px; }
        .kd-side-thumb img { max-height: 120px; }
        .main-slider-controls { display: none; }
    }
</style>
@endpush

@section('content')

    <!-- HERO SECTION -->
    <div class="hero-section">
        
        <!-- HEADER (Transparent) -->
        @include('partials.header', ['style' => 'transparent'])

        <!-- ARKA PLAN MEDYA KATMANI (VİDEO VEYA RESİM) -->
        <div class="hero-background">
            @if($sliders->isNotEmpty() && $sliders->first()->video_path)
                <!-- Eğer ilk slider öğesinde video varsa video oynat -->
                <video autoplay muted loop playsinline poster="{{ asset('storage/' . $sliders->first()->image_path) }}">
                    <source src="{{ asset('storage/' . $sliders->first()->video_path) }}" type="video/mp4">
                    Tarayıcınız video etiketini desteklemiyor.
                </video>
            @elseif($sliders->isNotEmpty() && $sliders->first()->image_path)
                <!-- Video yoksa ama slider resmi varsa onu kullan -->
                <img src="{{ asset('storage/' . $sliders->first()->image_path) }}" alt="Arkaplan">
            @else
                <!-- Slider görseli yoksa -->
                <img src="{{ asset('images/logo.png') }}" alt="" style="object-fit: cover;">
            @endif
            
            <!-- Karartma Katmanı -->
            <div class="hero-overlay"></div>
        </div>

        <!-- DASHBOARD (Kare Butonlar & Sol Slider) -->
        <div id="main-content" tabindex="-1" class="container-fluid bottom-section">
            <div class="row align-items-end position-relative">
                
                <!-- SOL: küçük vitrin — «Kırklareli'den Haberler» ile aynı kaynaktan son 3 genel duyuru -->
                <div class="col-xl-3 col-lg-3 col-12 d-flex justify-content-center justify-content-lg-start order-2 order-lg-1 mb-3 mb-lg-0 ps-lg-4">
                    @if($kirklareliHeroMini->isNotEmpty())
                    <div class="news-container">
                        <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                            <button class="slider-arrow prev-arrow" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev"><i class="fas fa-chevron-left"></i></button>
                            <button class="slider-arrow next-arrow" type="button" data-bs-target="#newsCarousel" data-bs-slide="next"><i class="fas fa-chevron-right"></i></button>
                            
                            <div class="carousel-inner">
                                @foreach($kirklareliHeroMini as $index => $ann)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ $ann->coverImageUrl() }}" alt="{{ $ann->title }}" class="news-img">
                                    <div class="news-caption">
                                        <span class="news-tag">DUYURU</span>
                                        <div class="news-text">{{ Str::limit($ann->title, 80) }}</div>
                                        <a href="{{ route('announcement.show', $ann->slug) }}" class="read-more-link">İncele <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- ORTA: KARE BUTONLAR (Dinamik) -->
                <div class="col-xl-7 col-lg-7 col-12 d-flex flex-column justify-content-end align-items-center order-1 order-lg-2 mb-4 mb-lg-0">
                    <div class="buttons-container">
                        @forelse($quickLinks as $link)
                        <a href="{{ $link->url }}" class="box-btn">
                            <i class="{{ $link->icon_class }}"></i>
                            <span>{!! nl2br($link->title) !!}</span>
                        </a>
                        @empty
                        <a href="#" class="box-btn"><i class="fas fa-plus"></i><span>Panelden<br>Ekle</span></a>
                        @endforelse
                    </div>
                </div>

                <!-- SAĞ: BAŞKAN (Dinamik) -->
                <div class="col-xl-2 col-lg-2 d-none d-lg-block order-3 p-0">
                    @if($mayor)
                    <div class="mayor-wrapper">
                        <!-- Storage Klasöründen Görsel -->
                        <img src="{{ asset('storage/' . $mayor->image_path) }}" alt="{{ $mayor->name }}" class="mayor-img">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- KIRKLARELİ'DEN HABERLER: son 5 genel duyuru (slider) -->
    @if($kirklareliFromDuyurular->isNotEmpty())
    <div class="latest-news-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">KIRKLARELİ'DEN HABERLER</h2>
                <a href="{{ route('announcements.index', ['tip' => 'duyuru']) }}" class="btn-view-all">Tümünü Gör <i class="fas fa-arrow-right ms-2"></i></a>
            </div>

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <div id="headlineSlider" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="9000" data-bs-pause="false">
                        <div class="carousel-inner">
                            @foreach($kirklareliFromDuyurular as $index => $ann)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="headline-card" onclick="location.href='{{ route('announcement.show', $ann->slug) }}'">
                                    <div class="headline-cover">
                                        <img src="{{ $ann->coverImageUrl() }}" alt="{{ $ann->title }}">
                                    </div>
                                    <div class="headline-body">
                                        <span class="headline-tag">DUYURU</span>
                                        <h3 class="headline-title">{{ $ann->title }}</h3>
                                        <div class="headline-text">{{ Str::limit(strip_tags((string) $ann->content), 120) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="main-slider-controls">
                            <button class="slider-btn" type="button" data-bs-target="#headlineSlider" data-bs-slide="prev"><i class="fas fa-chevron-left"></i></button>
                            <button class="slider-btn" type="button" data-bs-target="#headlineSlider" data-bs-slide="next"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="headline-thumbs">
                        @foreach($kirklareliFromDuyurular as $index => $ann)
                        <div class="headline-thumb-item {{ $index == 0 ? 'active' : '' }}" data-bs-target="#headlineSlider" data-bs-slide-to="{{ $index }}" role="button" tabindex="0">
                            <img src="{{ $ann->coverImageUrl() }}" alt="">
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-5">
                    @foreach($kirklareliFromDuyurular as $index => $ann)
                    <a href="{{ route('announcement.show', $ann->slug) }}" class="kd-side-card {{ $index === 0 ? 'active' : '' }}">
                        <div class="kd-side-thumb">
                            <img src="{{ $ann->coverImageUrl() }}" alt="">
                        </div>
                        <div class="kd-side-body">
                            <div class="kd-side-title">{{ $ann->title }}</div>
                            <div class="kd-side-meta"><i class="far fa-calendar-alt me-1"></i>{{ $ann->date->format('d.m.Y') }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- DUYURULAR BÖLÜMÜ (3 Sütun Dinamik) -->
    <div class="announcements-section">
        <div class="container">
            <div class="row g-4">
                <!-- Sütun 1: Genel Duyurular -->
                <div class="col-lg-4">
                    <div class="ann-card">
                        <div class="ann-header header-blue">
                            <i class="fas fa-bullhorn text-white"></i> <h3>DUYURULAR</h3>
                        </div>
                        <ul class="ann-list">
                            @forelse($generalAnnouncements as $ann)
                            <li class="ann-item">
                                <a href="{{ route('announcement.show', $ann->slug) }}" class="ann-link">
                                    <div class="ann-text">{{ $ann->title }}</div>
                                    <div class="ann-date"><i class="far fa-calendar-alt" aria-hidden="true"></i>{{ $ann->date->format('d.m.Y') }}</div>
                                </a>
                            </li>
                            @empty
                            <li class="ann-item text-muted small">Üst vitrinde son duyurular listelenmiştir; arşiv için «Tüm Duyurular»a bakınız.</li>
                            @endforelse
                        </ul>
                        <div class="ann-footer"><a href="{{ route('announcements.index', ['tip' => 'duyuru']) }}" class="btn-ann-all">Tüm Duyurular</a></div>
                    </div>
                </div>

                <!-- Sütun 2: Resmi İlanlar -->
                <div class="col-lg-4">
                    <div class="ann-card">
                        <div class="ann-header header-orange">
                            <i class="fas fa-file-contract text-white"></i> <h3>RESMİ İLANLAR</h3>
                        </div>
                        <ul class="ann-list">
                            @foreach($officialAds as $ann)
                            <li class="ann-item">
                                <a href="{{ route('announcement.show', $ann->slug) }}" class="ann-link">
                                    <div class="ann-text">{{ $ann->title }}</div>
                                    <div class="ann-date"><i class="far fa-calendar-alt" aria-hidden="true"></i>{{ $ann->date->format('d.m.Y') }}</div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="ann-footer"><a href="{{ route('announcements.index', ['tip' => 'resmi']) }}" class="btn-ann-all">Tüm Resmi İlanlar</a></div>
                    </div>
                </div>

                <!-- Sütun 3: İhale Duyuruları -->
                <div class="col-lg-4">
                    <div class="ann-card">
                        <div class="ann-header header-dark">
                            <i class="fas fa-gavel text-white"></i> <h3>İHALE DUYURULARI</h3>
                        </div>
                        <ul class="ann-list">
                            @foreach($tenders as $ann)
                            <li class="ann-item">
                                <a href="{{ route('announcement.show', $ann->slug) }}" class="ann-link">
                                    <div class="ann-text">{{ $ann->title }}</div>
                                    <div class="ann-date"><i class="far fa-calendar-alt" aria-hidden="true"></i>{{ $ann->date->format('d.m.Y') }}</div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="ann-footer"><a href="{{ route('announcements.index', ['tip' => 'ihale']) }}" class="btn-ann-all">Tüm İhaleler</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
<script>
    // Slider ve Thumbnail Senkronizasyonu
    var headlineSlider = document.getElementById('headlineSlider');
    var thumbs = document.querySelectorAll('.headline-thumb-item');
    var sideCards = document.querySelectorAll('.kd-side-card');
    if (headlineSlider && thumbs.length > 0) {
        headlineSlider.addEventListener('slide.bs.carousel', function (e) {
            var slideIndex = e.to;
            thumbs.forEach(function (thumb, i) { thumb.classList.toggle('active', i === slideIndex); });
            sideCards.forEach(function (card, i) { card.classList.toggle('active', i === slideIndex); });
        });
    }
</script>


@endpush