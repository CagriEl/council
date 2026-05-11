<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $announcement->title }} - T.C. Kırklareli Belediyesi</title>
      @include('layouts.frontend-head')
    @php
        $annContentRaw = \App\Support\HtmlContentSanitizer::stripKaynakSayfayiAcBlocks((string) $announcement->content);
        $annCtas = \App\Support\AnnouncementContentCtas::pullResmiDuyurularListLinks($annContentRaw);
    @endphp
    <style>
        .ann-detail-cta-wrap { display: flex; flex-wrap: wrap; gap: 12px; align-items: stretch; }
        .ann-detail-cta-btn {
            display: inline-flex; align-items: center; gap: 12px;
            padding: 14px 22px; border-radius: 12px;
            background: linear-gradient(135deg, #e67e22 0%, #c0392b 100%);
            color: #fff !important; text-decoration: none !important; font-weight: 600; font-size: 0.95rem;
            box-shadow: 0 8px 22px rgba(192, 57, 43, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(255,255,255,0.22);
            max-width: 100%;
        }
        .ann-detail-cta-btn:hover { color: #fff !important; transform: translateY(-2px); box-shadow: 0 12px 28px rgba(192, 57, 43, 0.35); }
        .ann-detail-cta-icon { font-size: 1.2rem; opacity: 0.95; flex-shrink: 0; }
        .ann-detail-cta-text { flex: 1 1 auto; min-width: 0; line-height: 1.35; }
        .ann-detail-cta-arrow { font-size: 0.85rem; opacity: 0.85; flex-shrink: 0; }
    </style>

</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container mb-5">
        <div class="row">
            
            <!-- SOL SIDEBAR (DİĞER DUYURULAR - DİNAMİK) -->
            <div class="col-lg-3 mb-4 mb-lg-0">
                <h3 class="sidebar-title">DİĞER DUYURULAR</h3>
                
                @php
                    // Yan menü için son 5 duyuruyu çekiyoruz (Mevcut duyuru hariç)
                    $otherAnnouncements = \App\Models\Announcement::publishedForPublic()
                                            ->where('id', '!=', $announcement->id)
                                            ->latest('date')
                                            ->take(5)
                                            ->get();
                @endphp

                @foreach($otherAnnouncements as $item)
                    <a href="{{ route('announcement.show', $item->slug) }}" class="news-list-item news-card-default">
                        <span class="badge-cat">DUYURU</span>
                        <div class="side-news-title">{{ $item->title }}</div>
                        @php
                            $sideDate = \Illuminate\Support\Carbon::parse($item->date ?? $item->created_at)->locale('tr');
                        @endphp
                        <div class="side-news-date">{{ $sideDate->isoFormat('DD.MM.YYYY') }}</div>
                    </a>
                @endforeach
                
                @if($otherAnnouncements->isEmpty())
                    <p class="text-muted small">Başka güncel duyuru bulunmamaktadır.</p>
                @endif
            </div>

            <!-- SAĞ TARAF (HABER DETAYI) -->
            <div class="col-lg-9 ps-lg-5">
                
                <!-- Tarih -->
                @php
                    $annDisplayDate = \Illuminate\Support\Carbon::parse($announcement->published_at ?? $announcement->date ?? $announcement->created_at)->locale('tr');
                @endphp
                <div class="content-date">
                    <i class="far fa-calendar-alt me-2"></i>{{ $annDisplayDate->isoFormat('DD.MM.YYYY dddd') }}
                </div>

                <!-- Ana Başlık -->
                <h1 class="content-title">{{ $announcement->title }}</h1>

                <!-- İçerik ve Görsel Yapısı -->
                <div class="row">
                    <!-- Metin -->
                    <div class="col-lg-12 mb-4">
                        <img src="{{ $announcement->coverImageUrl() }}" alt="{{ $announcement->title }}" class="featured-image float-lg-end ms-lg-4 mb-3" style="max-width: 50%; height: auto;">

                        <div class="content-text">
                            {!! $annCtas['html'] !!}
                        </div>

                        <!-- PDF Dosya Linki -->
                        @if(!empty($announcement->file_path))
                            <div class="mt-4">
                                <a href="{{ asset('storage/' . $announcement->file_path) }}" target="_blank" class="pdf-download-box">
                                    <i class="fas fa-file-pdf fa-2x me-3"></i>
                                    <div>
                                        <div style="font-weight: 700;">EKİ GÖRÜNTÜLE</div>
                                        <div style="font-size: 0.85rem;">Dosyayı indirmek veya görüntülemek için tıklayın.</div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        @if(count($annCtas['externalLinks']) > 0)
                            <div class="ann-detail-cta-wrap mt-4">
                                <a href="{{ route('announcements.index', ['tip' => 'resmi']) }}" class="ann-detail-cta-btn">
                                    <span class="ann-detail-cta-icon" aria-hidden="true"><i class="fas fa-list-alt"></i></span>
                                    <span class="ann-detail-cta-text">Resmî duyurular listesi</span>
                                    <span class="ann-detail-cta-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')

</html>