<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $announcement->title }} - T.C. Kırklareli Belediyesi</title>
      @include('layouts.frontend-head')    

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
                        <div class="side-news-date">{{ $item->created_at->format('d.m.Y') }}</div>
                    </a>
                @endforeach
                
                @if($otherAnnouncements->isEmpty())
                    <p class="text-muted small">Başka güncel duyuru bulunmamaktadır.</p>
                @endif
            </div>

            <!-- SAĞ TARAF (HABER DETAYI) -->
            <div class="col-lg-9 ps-lg-5">
                
                <!-- Ana Başlık -->
                <h1 class="content-title">{{ $announcement->title }}</h1>

                <!-- İçerik ve Görsel Yapısı -->
                <div class="row">
                    <!-- Metin -->
                    <div class="col-lg-12 mb-4">
                        <!-- Eğer Resim Varsa -->
                        @if($announcement->image_path)
                            <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="{{ $announcement->title }}" class="featured-image float-lg-end ms-lg-4 mb-3" style="max-width: 50%; height: auto;">
                        @endif

                        @php
                            $contentWithoutSourceLink = preg_replace(
                                '/<p>\s*<a\b[^>]*>\s*Kaynak\s+sayfayı\s+aç\s*<\/a>\s*<\/p>|<a\b[^>]*>\s*Kaynak\s+sayfayı\s+aç\s*<\/a>/iu',
                                '',
                                $announcement->content ?? ''
                            );
                        @endphp
                        <div class="content-text">
                            {!! $contentWithoutSourceLink !!}
                        </div>

                        <!-- Ek Dosya Linki (PDF / ZIP) -->
                        @if(!empty($announcement->file_path))
                            @php
                                $ext = strtolower(pathinfo($announcement->file_path, PATHINFO_EXTENSION));
                                $isZip = $ext === 'zip';
                            @endphp
                            <div class="mt-4">
                                <a href="{{ asset('storage/' . $announcement->file_path) }}"
                                   @if($isZip) download @else target="_blank" @endif
                                   class="pdf-download-box">
                                    <i class="fas {{ $isZip ? 'fa-file-zipper' : 'fa-file-pdf' }} fa-2x me-3"></i>
                                    <div>
                                        <div style="font-weight: 700;">
                                            {{ $isZip ? 'ZIP DOSYASINI İNDİR' : 'EKİ GÖRÜNTÜLE' }}
                                        </div>
                                        <div style="font-size: 0.85rem;">
                                            {{ $isZip
                                                ? 'Sıkıştırılmış ek dosyayı indirmek için tıklayın.'
                                                : 'Dosyayı indirmek veya görüntülemek için tıklayın.' }}
                                        </div>
                                    </div>
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