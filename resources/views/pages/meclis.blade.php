<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belediye Meclis Üyeleri - T.C. Kırklareli Belediyesi</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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

        .logo-img { height: 64px; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)); }
        
        .internal-nav .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 600;
            margin-left: 15px;
            transition: color 0.2s;
        }
        .internal-nav .nav-link:hover { color: #3498db !important; }

        /* --- SAYFA BAŞLIĞI --- */
        .page-title-wrapper {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .page-title {
            font-weight: 800;
            font-size: 2.2rem;
            color: #1a3c6e;
            margin-bottom: 10px;
        }
        .page-subtitle {
            color: #777;
            font-size: 1.1rem;
        }
        .title-divider {
            width: 80px;
            height: 4px;
            background: #e74c3c;
            margin: 15px auto;
            border-radius: 2px;
        }

        /* --- MECLİS ÜYESİ KARTI --- */
        .member-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border: 1px solid #eee;
            position: relative;
        }

        .member-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        /* Fotoğraf Alanı */
        .member-img-wrapper {
            height: 320px;
            overflow: hidden;
            position: relative;
            background-color: #e9ecef;
        }

        .member-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            transition: transform 0.5s;
        }

        .member-card:hover .member-img {
            transform: scale(1.05);
        }

        /* Parti Rozeti (GÜNCELLENDİ) */
        .party-badge {
            display: inline-block; /* Artık akış içinde */
            margin-top: 10px; /* Ünvandan sonra boşluk */
            background: #f8f9fa; /* Hafif gri arka plan */
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            /* Hafif gölge */
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-transform: uppercase;
            /* Varsayılan sol çizgi */
            border-left: 3px solid #555; 
        }
        
        /* Renk Sınıfları (Fallback) */
        .badge-chp { color: #e30000; border-left-color: #e30000; }
        .badge-akp { color: #ff9d00; border-left-color: #ff9d00; }
        .badge-mhp { color: #cc0000; border-left-color: #cc0000; }
        .badge-baskan { color: #1a3c6e; border-left-color: #1a3c6e; }
        .badge-default { color: #555; border-left-color: #555; }

        /* Bilgi Alanı */
        .member-info {
            padding: 20px;
            text-align: center;
        }

        .member-name {
            font-weight: 800;
            font-size: 1.1rem;
            color: #1a3c6e;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .member-title {
            font-size: 0.9rem;
            color: #666;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .internal-header { text-align: center; border-radius: 0; }
            .internal-nav { justify-content: center !important; margin-top: 15px; }
            .logo-img { margin-bottom: 10px; }
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
    <!-- ANA İÇERİK -->
    <div class="container mb-5">
        
        <!-- Sayfa Başlığı -->
        <div class="page-title-wrapper">
            <h1 class="page-title">Belediye Meclis Üyeleri</h1>
            <div class="title-divider"></div>
            <p class="page-subtitle">Kırklareli Belediyesi 2024-2029 Dönemi Meclis Üyeleri</p>
        </div>

        <!-- Meclis Üyeleri Grid -->
        <div class="row g-4">
            
            @forelse($members as $member)
                @php
                    // Parti ismini al
                    $displayParty = $member->politicalParty->name ?? $member->party;
                    
                    // Panelden girilen özel rengi al
                    $partyColor = $member->politicalParty->color ?? null;
                    
                    // Fallback CSS sınıfı mantığı (Renk yoksa çalışır)
                    $badgeClass = 'badge-default';
                    
                    if ($displayParty) {
                        $partyNameUpper = Str::upper($displayParty);
                        
                        if (Str::contains($partyNameUpper, 'CHP')) {
                            $badgeClass = 'badge-chp';
                        } elseif (Str::contains($partyNameUpper, 'AK') || Str::contains($partyNameUpper, 'AKP')) {
                            $badgeClass = 'badge-akp';
                        } elseif (Str::contains($partyNameUpper, 'MHP')) {
                            $badgeClass = 'badge-mhp';
                        } elseif (Str::contains($partyNameUpper, 'BAŞKAN')) {
                            $badgeClass = 'badge-baskan';
                        }
                    }
                @endphp

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="member-card">
                        
                        <div class="member-img-wrapper">
                            <!-- Resim Varsa Göster, Yoksa Otomatik Avatar -->
                            <img src="{{ $member->image_path ? asset('storage/' . $member->image_path) : 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&size=500&background=random&color=fff' }}" 
                                 alt="{{ $member->name }}" 
                                 class="member-img">
                        </div>
                        
                        <div class="member-info">
                            <div class="member-name">{{ $member->name }}</div>
                            <div class="member-title">{{ $member->title ?? 'Meclis Üyesi' }}</div>

                            {{-- Parti İsmini Buraya Taşıdık --}}
                            @if($displayParty)
                                <div class="party-badge {{ $badgeClass }}" 
                                     @if($partyColor) style="border-left-color: {{ $partyColor }} !important; color: {{ $partyColor }} !important;" @endif>
                                    {{ $displayParty }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded shadow-sm border">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted fw-bold">Henüz meclis üyesi kaydı bulunmamaktadır.</h4>
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>