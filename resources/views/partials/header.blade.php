@php
    $headerClass = isset($style) && $style == 'solid' ? 'header-solid' : 'header-transparent';
@endphp

<div class="header-wrapper {{ $headerClass }}">
    <div class="contact-badge-wrapper d-none d-lg-flex align-items-center gap-3">
        <a href="https://www.mgm.gov.tr/tahmin/il-ve-ilceler.aspx?il=KIRKLARELİ" target="_blank" class="weather-badge">
            <i class="fas fa-cloud-sun"></i> <span>HAVA DURUMU</span>
        </a>
        <div class="contact-badge">
            <span>BİZE ULAŞIN</span>
            <div style="height: 15px; width: 1px; background: rgba(255,255,255,0.5);"></div>
            <i class="fas fa-phone-alt"></i>
            <span>444 01 39</span>
        </div>
    </div><br><br>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand d-lg-none d-flex align-items-center" href="{{ route('home') }}">
                <img src="https://yeni.kirklarelidev.com.tr/logo.png" alt="Logo" class="logo-img ">
                <img src="https://yeni.kirklarelidev.com.tr/atatr.png" alt="Logo" class="logo-img ">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto align-items-center custom-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('baskan') }}">BAŞKAN</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('mudurler') }}">Organizasyon Şeması</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('meclis') }}">MECLİS</a></li>

                </ul>

                <a class="navbar-brand d-none d-lg-flex center-brand" href="{{ route('home') }}">
                    <img src="https://yeni.kirklarelidev.com.tr/logo.png" alt="Logo" class="logo-img ">
                    <img src="https://yeni.kirklarelidev.com.tr/atatr.png" alt="Logo" class="logo-img ">

             </a>

                <ul class="navbar-nav ms-auto align-items-center custom-nav">
                    <li class="nav-item"><a class="nav-link" href="#">GÜNCEL</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('meclis-kararlari') }}">Meclis Kararları</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">KÜLTÜR - SANAT</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{route('iletisim')}}">İLETİŞİM</a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>