<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizasyon Şeması - T.C. Kırklareli Belediyesi</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        /* --- HEADER --- */
        .internal-header {
            background-color: #1a3c6e;
            border-bottom-left-radius: 28px;
            padding: 0.65rem 0;
            margin-bottom: 1rem;
            color: white;
        }
        .internal-header .navbar.navbar-dark {
            --bs-navbar-color: #ffffff;
            --bs-navbar-hover-color: #ffffff;
            --bs-navbar-active-color: #ffffff;
            --bs-navbar-disabled-color: rgba(255, 255, 255, 0.55);
        }
        .logo-img { height: 64px; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)); }
        .internal-nav .nav-link { color: #ffffff !important; font-weight: 600; margin-left: 15px; transition: color 0.2s, text-decoration 0.2s; opacity: 1; }
        .internal-nav .nav-link:hover, .internal-nav .nav-link:focus { color: #ffffff !important; text-decoration: underline; text-underline-offset: 0.2em; text-decoration-thickness: 2px; }

        /* --- SAYFA BAŞLIĞI --- */
        .page-title-wrapper { text-align: center; margin-bottom: 1.75rem; }
        .page-title { font-weight: 800; font-size: 2.2rem; color: #1a3c6e; margin-bottom: 10px; }
        .title-divider { width: 80px; height: 4px; background: #e74c3c; margin: 15px auto; border-radius: 2px; }

        /* --- ORGANİZASYON AĞACI CSS (TAM ORTALI) --- */
        .org-chart-wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
            overflow-x: auto; /* Mobilde taşarsa kaydır */
            padding-bottom: 50px;
        }

        .tree {
            display: table; /* Merkezlemek için */
            margin: 0 auto;
        }

        .tree ul {
            padding-top: 20px; 
            position: relative;
            transition: all 0.5s;
            display: flex;
            justify-content: center; /* ÇOCUKLARI ORTALA */
        }

        .tree li {
            float: left; text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 10px 0 10px;
            transition: all 0.5s;
        }

        /* Çizgiler */
        .tree li::before, .tree li::after {
            content: '';
            position: absolute; top: 0; right: 50%;
            border-top: 2px solid #ccc;
            width: 50%; height: 20px;
        }
        .tree li::after {
            right: auto; left: 50%;
            border-left: 2px solid #ccc;
        }

        /* Tek çocuk varsa çizgiyi gizle */
        .tree li:only-child::after, .tree li:only-child::before {
            display: none;
        }
        .tree li:only-child { padding-top: 0; }

        /* İlk ve son çocukların çizgilerini düzelt */
        .tree li:first-child::before, .tree li:last-child::after {
            border: 0 none;
        }
        .tree li:last-child::before{
            border-right: 2px solid #ccc;
            border-radius: 0 5px 0 0;
        }
        .tree li:first-child::after{
            border-radius: 5px 0 0 0;
        }

        /* Dikey inen çizgiler */
        .tree ul ul::before{
            content: '';
            position: absolute; top: 0; left: 50%;
            border-left: 2px solid #ccc;
            width: 0; height: 20px;
        }

        /* --- MÜDÜRLÜKLER İÇİN DİKEY LİSTELEME --- */
        .tree ul.vertical-nodes {
            flex-direction: column; /* Alt alta diz */
            align-items: center;
            padding-top: 20px;
            position: relative;
        }

        /* Dikey Omurga Çizgisi */
        .tree ul.vertical-nodes::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            border-left: 2px solid #ccc;
            height: 100%; /* Listenin sonuna kadar inen çizgi */
            width: 0;
            z-index: 0;
            transform: translateX(-50%);
        }

        /* Liste Elemanları (Müdürlük Kartları) */
        .tree ul.vertical-nodes li {
            float: none; /* Yan yana dizilmeyi iptal et */
            padding: 10px 0; /* Dikey boşluk */
            position: relative;
            z-index: 1; /* Çizginin üstünde durması için */
        }

        /* Standart ağaç çizgilerini (yatay kollar) iptal et */
        .tree ul.vertical-nodes li::before, 
        .tree ul.vertical-nodes li::after {
            display: none;
        }

        /* Son elemandan sonra çizginin sarkmaması için */
        .tree ul.vertical-nodes li:last-child {
            padding-bottom: 0;
            background: #f4f7f6; /* Sayfa rengiyle aynı yapıp alttan sarkan çizgiyi kapatabiliriz */
        }
        /* Son elemanın altındaki çizgiyi kapatan yama */
        .tree ul.vertical-nodes::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 4px;
            height: 20px; /* Son kartın yarısı kadar */
            background: #f4f7f6; /* Arka plan rengiyle aynı */
            transform: translateX(-50%);
            z-index: 0;
        }


        /* --- KUTU TASARIMI --- */
        .org-card {
            background: white;
            border: 1px solid #e0e0e0;
            padding: 15px;
            text-decoration: none;
            color: #333;
            display: inline-block;
            border-radius: 8px;
            transition: all 0.3s;
            /* TÜM KUTU GENİŞLİKLERİ AYNI OLSUN */
            width: 220px; /* Sabit Genişlik */
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: relative;
            z-index: 10;
        }

        .org-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transform: translateY(-3px);
            border-color: #1a3c6e;
        }

        .org-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 10px auto;
            border: 3px solid #f8f9fa;
            display: block;
        }

        .org-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: #1a3c6e;
            display: block;
            line-height: 1.2;
            margin-bottom: 3px;
        }

        .org-title {
            font-size: 0.75rem;
            color: #666;
            font-weight: 500;
            display: block;
        }

        /* --- TİPLERE GÖRE RENKLER --- */
        /* Başkan */
        .type-mayor {
            background: #1a3c6e;
            color: white;
            border: 2px solid #1a3c6e;
            /* width: 220px; artık global .org-card içinde */
        }
        .type-mayor .org-name { color: white; font-size: 1.1rem; }
        .type-mayor .org-title { color: #ccc; }
        .type-mayor:hover { background: #15325e; }

        /* Başkan Yrd. */
        .type-vice_mayor {
            border-top: 4px solid #e74c3c; /* Kırmızı Şerit */
        }

        /* Müdürlük */
        .type-unit {
            border-top: 4px solid #f1c40f; /* Sarı Şerit */
        }

        /* --- YATAY ÇİZGİ DÜZELTMESİ --- */
        .tree ul {
            flex-wrap: nowrap;
        }

    </style>
</head>
<body>

@include('partials.accessibility')

    <!-- HEADER -->
    <div class="internal-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 text-center text-lg-start">
                    <a href="/">
                        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="logo-img">
                    </a>
                </div>
                <div class="col-lg-9">
                    <nav class="navbar navbar-expand-lg navbar-dark justify-content-center justify-content-lg-end">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse flex-grow-0" id="navbarNav">
                            <ul class="navbar-nav internal-nav">
                                <li class="nav-item"><a class="nav-link" href="{{ route('baskan') }}">BAŞKAN</a></li>
                                <li class="nav-item"><a class="nav-link active" href="#">KURUMSAL</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">KIRKLARELİ</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">GÜNCEL</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">HİZMET REHBERİ</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">E-BELEDİYE</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('iletisim') }}">İLETİŞİM</a></li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

<main id="main-content" tabindex="-1" class="outline-none">
    <!-- İÇERİK -->
    <div class="container mb-5">
        
        <div class="page-title-wrapper">
            <h1 class="page-title">Organizasyon Şeması</h1>
            <div class="title-divider"></div>
            <p class="page-subtitle text-muted">Kırklareli Belediyesi İdari Teşkilat Yapısı</p>
        </div>

        <div class="org-chart-wrapper">
            <div class="tree">
                <ul>
                    {{-- 1. SEVİYE: BELEDİYE BAŞKANI --}}
                    <li>
                        @php
                            $mayorName = $mayor?->name ?? 'Derya BULUT';
                            $mayorTitle = $mayor?->title ?? 'Belediye Başkanı';
                            $mayorImg = ($mayor?->image_path)
                                ? asset('storage/'.$mayor->image_path)
                                : asset('assets/baskan-small.png');
                        @endphp
                        <a href="{{ route('baskan') }}" class="org-card type-mayor">
                            <img src="{{ $mayorImg }}"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($mayorName) }}&background=fff&color=1a3c6e'"
                                 alt="{{ $mayorName }}"
                                 class="org-img">
                            <span class="org-name">{{ $mayorName }}</span>
                            <span class="org-title">{{ $mayorTitle }}</span>
                        </a>

                        @if(isset($mayorDirectorates) && $mayorDirectorates->count() > 0)
                            <ul class="vertical-nodes">
                                @foreach($mayorDirectorates as $directorate)
                                    <li>
                                        <a href="{{ route('mudurluk.detay', $directorate->slug) }}" class="org-card type-unit">
                                            <span class="org-name">{{ $directorate->name }}</span>
                                            @if($directorate->manager_name)
                                                <span class="org-title">{{ $directorate->manager_name }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if($vicePresidents->count() > 0)
                            <ul>
                                {{-- 2. SEVİYE: BAŞKAN YARDIMCILARI (YAN YANA) --}}
                                @foreach($vicePresidents as $vicePresident)
                                    <li>
                                        <a href="#" class="org-card type-vice_mayor">
                                            @if($vicePresident->image_path)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($vicePresident->image_path) }}" class="org-img" alt="">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($vicePresident->name ?? 'Baskan Yrd') }}&background=f0f2f5&color=1a3c6e" class="org-img" alt="">
                                            @endif
                                            <span class="org-name">{{ $vicePresident->name }}</span>
                                            <span class="org-title">{{ $vicePresident->title ?? 'Belediye Başkan Yrd.' }}</span>
                                        </a>

                                        @if($vicePresident->directorates->count() > 0)
                                            <ul class="vertical-nodes">
                                                @foreach($vicePresident->directorates as $directorate)
                                                    <li>
                                                        <a href="{{ route('mudurluk.detay', $directorate->slug) }}" class="org-card type-unit">
                                                            <span class="org-name">{{ $directorate->name }}</span>
                                                            @if($directorate->manager_name)
                                                                <span class="org-title">{{ $directorate->manager_name }}</span>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

    </div>
</main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>