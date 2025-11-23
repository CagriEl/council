@extends('layouts.header')

{{-- HATA BURADAYDI: $directorate yerine $mudurluk yaptık --}}
@section('title', $mudurluk->name . ' - T.C. Kırklareli Belediyesi')

@section('content')

@push('styles')
<style>
    /* HEADER (Standart Detay Header) */
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
    
    .detail-nav .nav-link {
        color: rgba(255,255,255,0.9) !important;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        margin-left: 15px;
        transition: color 0.2s;
    }
    .detail-nav .nav-link:hover { color: #3498db !important; }

    .contact-info {
        color: white;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: flex-end;
        margin-bottom: 10px;
    }
    .phone-pill {
        background: white;
        color: #1a3c6e;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 800;
    }

    /* SOL KENAR ÇUBUĞU (Müdür Bilgisi) */
    .dept-sidebar-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        border: 1px solid #eee;
    }
    
    .dept-manager-img {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid #e9ecef;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .dept-manager-name {
        font-weight: 800;
        color: #1a3c6e;
        font-size: 1.2rem;
        margin-bottom: 5px;
    }
    
    .dept-manager-title {
        color: #777;
        font-size: 0.9rem;
        margin-bottom: 20px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .contact-list {
        list-style: none;
        padding: 0;
        text-align: left;
        margin-top: 20px;
    }
    
    .contact-list li {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        color: #555;
        font-size: 0.95rem;
    }
    
    .contact-list i {
        width: 35px;
        height: 35px;
        background: #eaf2ff;
        color: #0052cc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* SAĞ İÇERİK ALANI */
    .content-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        min-height: 400px;
    }
    
    .page-title {
        font-weight: 800;
        color: #1a3c6e;
        margin-bottom: 20px;
        font-size: 2rem;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 2px solid #f1f1f1;
        padding-bottom: 15px;
    }
    
    .page-title i {
        color: #e74c3c;
    }

    .content-text {
        line-height: 1.8;
        color: #444;
        font-size: 1.05rem;
    }
    
    /* Panelden gelen HTML içeriklerin stil düzeltmeleri */
    .content-text h2, .content-text h3 { color: #1a3c6e; font-weight: 700; margin-top: 25px; margin-bottom: 15px; }
    .content-text ul { padding-left: 20px; margin-bottom: 20px; list-style: none; }
    .content-text li { position: relative; padding-left: 25px; margin-bottom: 10px; }
    .content-text li::before {
        content: '\f00c'; /* FontAwesome check */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        left: 0;
        color: #27ae60;
    }
    
    /* Görev ve Yetkiler Kutusu */
    .mission-vision-box {
        background: #f8faff;
        border-left: 5px solid #00c6ff;
        padding: 25px;
        margin: 30px 0;
        border-radius: 0 10px 10px 0;
    }
    .mv-title {
        font-weight: 700;
        color: #1a3c6e;
        margin-bottom: 10px;
        font-size: 1.2rem;
    }

    @media (max-width: 992px) {
        .detail-header-bg { border-radius: 0; }
        .detail-logo-container { justify-content: center; margin-bottom: 1rem; }
        .contact-info { justify-content: center; }
        .detail-nav { text-align: center; }
        .page-title { font-size: 1.5rem; flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush


<!-- İÇERİK -->
<div class="container mb-5">
    <div class="row">
        
        <!-- SOL: Müdür Bilgisi -->
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="dept-sidebar-card">
                <!-- Müdür Görseli -->
                @if($mudurluk->manager_image)
                    <img src="{{ Storage::url($mudurluk->manager_image) }}" alt="{{ $mudurluk->manager_name }}" class="dept-manager-img">
                @else
                    <!-- Fotoğraf yoksa baş harflerden avatar oluştur -->
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mudurluk->manager_name ?? $mudurluk->name) }}&background=0052cc&color=fff&size=140" alt="{{ $mudurluk->manager_name }}" class="dept-manager-img">
                @endif

                <div class="dept-manager-name">{{ $mudurluk->manager_name }}</div>
                <div class="dept-manager-title">{{ $mudurluk->manager_title ?? 'Müdür V.' }}</div>
                <hr>
                
                <ul class="contact-list">
                    @if($mudurluk->phone)
                        <li><i class="fas fa-phone"></i> {{ $mudurluk->phone }}</li>
                    @else
                        <li><i class="fas fa-phone"></i> 444 01 39</li>
                    @endif

                    @if($mudurluk->email)
                        <li><i class="fas fa-envelope"></i> {{ $mudurluk->email }}</li>
                    @endif

                    <li><i class="fas fa-map-marker-alt"></i> Belediye Ana Hizmet Binası</li>
                </ul>
            </div>
        </div>

        <!-- SAĞ: Müdürlük Hakkında -->
        <div class="col-lg-8">
            <div class="content-card">
                <!-- Başlık -->
                <h1 class="page-title">
                    <i class="fas fa-building"></i>
                    {{ $mudurluk->name }}
                </h1>
                
                <!-- İçerik (Panelden gelen HTML) -->
                <div class="content-text">
                    @if($mudurluk->description)
                        {!! $mudurluk->description !!}
                    @else
                        <p>Bu müdürlük için henüz detaylı bilgi girilmemiştir.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@include('layouts.footer')
@endsection


