@php
    $headerClass = isset($style) && $style == 'solid' ? 'header-solid' : 'header-transparent';
@endphp

<div class="header-wrapper {{ $headerClass }}">
    <div class="header-topbar">
        <div class="container-fluid px-lg-5">
            <div class="header-topbar-inner">
                <a href="https://www.mgm.gov.tr/tahmin/il-ve-ilceler.aspx?il=KIRKLARELİ" target="_blank" rel="noopener noreferrer" class="header-topbar-weather">
                    <i class="fas fa-cloud-sun" aria-hidden="true"></i>
                    <span>HAVA DURUMU</span>
                </a>
                <a href="tel:4440139" class="header-topbar-contact" aria-label="Bize ulaşın: 444 01 39">
                    <span class="header-topbar-contact-label d-none d-sm-inline">BİZE ULAŞIN</span>
                    <span class="header-topbar-sep d-none d-sm-inline" aria-hidden="true"></span>
                    <i class="fas fa-phone-alt" aria-hidden="true"></i>
                    <span class="header-topbar-phone">444 01 39</span>
                </a>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark py-lg-1">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand d-lg-none d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img"> &nbsp;
                <img src="{{ asset('images/atatr.png') }}" alt="Logo" class="logo-img">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto align-items-center custom-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('baskan') }}">Başkan</a></li>
                    @include('partials.header-nav-items', ['menus' => ($headerMenus ?? collect())->whereIn('title', ['Kurumsal', 'Mevzuat'])])
                    <li class="nav-item"><a class="nav-link" href="{{ route('news.index') }}">Haberler</a></li>
                </ul>

                <a class="navbar-brand d-none d-lg-flex center-brand" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img"> &nbsp;
                    <img src="{{ asset('images/atatr.png') }}" alt="Logo" class="logo-img">
                </a>

                <ul class="navbar-nav ms-auto align-items-center custom-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('announcements.index') }}">Duyurular</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('transparency.show') }}">
                            Şeffaflık ve Hesap Verilebilirlik
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('iletisim') }}">İletişim</a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>
