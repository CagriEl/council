<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'T.C. Kırklareli Belediyesi')</title>

    @include('layouts.frontend-head')
</head>

<body>
    @include('partials.accessibility')

    {{-- HEADER (müdürlük detay vb. @extends ile kullanılan sayfalar) --}}
    <div class="container-fluid p-0">
        <div class="detail-header-bg">
            <div class="container">
                <div class="row align-items-center header-top-row">
                    <div class="col-lg-3 col-12 detail-logo-container">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img"> &nbsp;
                             <img src="{{ asset('images/atatr.png') }}" alt="Logo" class="logo-img">
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
                                        <li class="nav-item"><a class="nav-link" href="{{ route('baskan') }}">BAŞKAN</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('mudurler') }}">KURUMSAL</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">KIRKLARELİ</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('meclis-kararlari') }}">Meclis Karalari</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('news.index') }}">HABERLER</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('announcements.index') }}">DUYURULAR</a></li>
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

    {{-- CONTENT --}}
    <main id="main-content" tabindex="-1" class="outline-none">
    @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
