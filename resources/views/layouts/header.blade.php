<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Sayfa Başlığı Dinamik Olarak Gelir (Örn: Müdürlüklerimiz) --}}
    <title>@yield('title', 'T.C. Kırklareli Belediyesi')</title>

    {{-- Temel CSS Kütüphaneleri --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    {{-- Genel Stil Tanımları (Sizin Gönderdiğiniz Header Stilleri) --}}
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        /* --- HEADER --- */
        .detail-header-bg {
            background: linear-gradient(90deg, #0052cc, #00c6ff);
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 0;
            margin-bottom: 3rem;
            position: relative;
            overflow: visible;
        }
        .header-top-row { padding-top: 1rem; padding-bottom: 1rem; }
        .detail-logo-container { display: flex; align-items: center; }
        .detail-logo-img { max-height: 120px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)); }
        .detail-nav .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 600; margin-left: 15px; }
        .contact-info { color: white; font-weight: 700; display: flex; align-items: center; gap: 10px; justify-content: flex-end; margin-bottom: 10px; }
        .phone-pill { background: white; color: #1a3c6e; padding: 5px 15px; border-radius: 20px; font-weight: 800; }
        
        /* Mobile Düzenlemeler */
        @media (max-width: 992px) {
            .detail-header-bg { border-radius: 0; }
            .detail-logo-container { justify-content: center; margin-bottom: 1rem; }
            .contact-info { justify-content: center; }
            .detail-nav { text-align: center; }
        }
    </style>

    {{-- Sayfaya Özel Stiller Buraya Eklenir (Örn: mudurler sayfasının kart stilleri) --}}
    @stack('styles')
</head>
<body>

    {{-- ************************************************* --}}
    {{-- A. HEADER (Tüm Sayfalarda Ortak Alan) --}}
    {{-- ************************************************* --}}
    <div class="container-fluid p-0">
        <div class="detail-header-bg">
            <div class="container">
                <div class="row align-items-center header-top-row">
                    <div class="col-lg-3 col-12 detail-logo-container">
                        <a href="{{ url('/') }}">
                            {{-- Bu logo dosyasını projenize eklemelisiniz --}}
                            <img src="/assets/images/logo.png" alt="Logo" class="detail-logo-img">
                        </a>
                    </div>
                    <div class="col-lg-9 col-12">
                        <div class="contact-info d-none d-lg-flex">
                            <span>BİZE ULAŞIN</span>
                            <i class="fas fa-headset"></i>
                            <span class="phone-pill">444 01 39</span>
                        </div>
                        <nav class="navbar navbar-expand-lg navbar-dark p-0">
                            <div class="container-fluid justify-content-center justify-content-lg-end p-0">
                                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#detailNav">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse flex-grow-0" id="detailNav">
                                    <ul class="navbar-nav detail-nav">
                                        <li class="nav-item"><a class="nav-link" href="baskan.html">BAŞKAN</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('mudurlukler') }}">KURUMSAL</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">KIRKLARELİ</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">GÜNCEL</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">HİZMET REHBERİ</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">E-BELEDİYE</a></li>
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ************************************************* --}}
    {{-- B. İÇERİK (Dinamik Alan) --}}
    {{-- Buraya @section('content') ile gelen sayfa içeriği yerleşir --}}
    {{-- ************************************************* --}}
    @yield('content')


    {{-- ************************************************* --}}
    {{-- C. FOOTER (Tüm Sayfalarda Ortak Alan) --}}
    {{-- ************************************************* --}

    {{-- Temel JS Kütüphanesi --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Sayfaya Özel Scriptler Buraya Eklenir --}}
    @stack('scripts')
</body>
</html>