<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'T.C. Kırklareli Belediyesi')</title>

    <script>document.documentElement.classList.add('kb-loading');</script>
    <style>html.kb-loading, html.kb-loading body { overflow: hidden !important; }</style>

    <!-- CSS Kütüphaneleri -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- Genel Stiller -->
    <style>
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; background-color: #fff; display: flex; flex-direction: column; min-height: 100vh; }
        
        /* HEADER & NAV STİLLERİ (Ortak) */
        .header-wrapper { position: relative; z-index: 50; }
        .header-wrapper.header-transparent { padding-top: 0; padding-bottom: 2rem; }
        .header-wrapper.header-solid { padding-top: 0; padding-bottom: 0.5rem; }
        .header-transparent { background: linear-gradient(to bottom, rgba(0,0,0,0.9), transparent); }
        .header-solid { background: linear-gradient(90deg, #0052cc, #00c6ff); border-bottom-left-radius: 24px; margin-bottom: 1rem; }

        /* Üst şerit: hava + iletişim */
        .header-topbar {
            width: 100%;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .header-wrapper.header-solid .header-topbar {
            background: rgba(0, 0, 0, 0.2);
        }
        .header-wrapper.header-transparent .header-topbar {
            background: rgba(0, 0, 0, 0.4);
        }
        .header-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.35rem 1rem;
            padding: 0.35rem 0;
            min-height: 2.25rem;
        }
        .header-topbar-weather {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.3rem 0.65rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            text-decoration: none;
            color: #fff !important;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.22);
            transition: background 0.2s, color 0.2s;
        }
        .header-topbar-weather:hover {
            background: rgba(255, 255, 255, 0.95);
            color: #0052cc !important;
        }
        .header-topbar-contact {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            color: #fff !important;
            text-decoration: none;
            white-space: nowrap;
        }
        .header-topbar-contact:hover {
            color: #e0f4ff !important;
        }
        .header-topbar-contact-label {
            opacity: 0.92;
        }
        .header-topbar-sep {
            width: 1px;
            height: 12px;
            background: rgba(255, 255, 255, 0.45);
            flex-shrink: 0;
        }
        .header-topbar-phone {
            font-weight: 800;
            font-size: 0.8rem;
        }

        .center-brand { display: flex; align-items: center; justify-content: center; margin: 0 20px; transition: transform 0.3s; }
        .center-brand:hover { transform: scale(1.05); }
        .logo-img { max-height: 120px; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5)); }
        .header-wrapper.header-solid .logo-img { max-height: 68px; }
        .atatr-img { max-height: 80px; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5)); margin-left: 10px; }
        .header-wrapper.header-solid .atatr-img { max-height: 52px; }

        .custom-nav .nav-link { color: #ffffff !important; font-weight: 700; font-size: 1rem; text-transform: capitalize; padding: 0.5rem 1.2rem; margin: 0 2px; transition: all 0.3s; text-shadow: 0 2px 4px rgba(0,0,0,0.8); position: relative; opacity: 1 !important; }
        .header-wrapper.header-solid .custom-nav .nav-link { font-size: 0.82rem; padding: 0.35rem 0.75rem; }
        .custom-nav .nav-link:not(.dropdown-toggle)::after { content: ''; display: block; width: 0; height: 2px; background: #00c6ff; transition: width .3s; margin: 0 auto; }
        .custom-nav .nav-link:not(.dropdown-toggle):hover::after { width: 100%; }
        .custom-nav .nav-link:hover { color: #ffffff !important; }

        .custom-nav .dropdown-menu {
            background: #fff;
            border: none;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            padding: 0.35rem 0;
            min-width: 220px;
        }
        .custom-nav .dropdown-item {
            font-size: 0.85rem;
            font-weight: 600;
            color: #112240;
            padding: 0.45rem 1rem;
        }
        .custom-nav .dropdown-item:hover {
            background: #e8f4ff;
            color: #0052cc;
        }
        .custom-nav .dropdown-toggle::after { display: none; }

        .menu-caret {
            margin-left: 0.45rem;
            font-size: 0.85em;
            opacity: 0.95;
            display: inline-flex;
            align-items: center;
        }

        /* FOOTER STİLLERİ */
        footer { background-color: #112240; color: #fff; padding-top: 70px; position: relative; font-size: 0.9rem; margin-top: auto; }
        .footer-logo { height: 100px; margin-bottom: 20px; filter: brightness(0) invert(1); }
        .footer-desc { opacity: 0.7; line-height: 1.6; margin-bottom: 20px; }
        .footer-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 25px; position: relative; padding-bottom: 10px; }
        .footer-title::after { content: ''; position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: #00c6ff; }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .footer-links a:hover { color: #00c6ff; padding-left: 5px; }
        .footer-contact li { display: flex; gap: 15px; margin-bottom: 20px; color: rgba(255,255,255,0.8); }
        .footer-contact i { font-size: 1.2rem; color: #00c6ff; margin-top: 3px; }
        .social-links { display: flex; gap: 10px; margin-top: 20px; }
        .social-link { width: 40px; height: 40px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; text-decoration: none; transition: 0.3s; }
        .social-link:hover { background: #00c6ff; transform: translateY(-3px); }
        .copyright { background: #0a162b; padding: 20px 0; margin-top: 50px; text-align: center; font-size: 0.85rem; opacity: 0.6; border-top: 1px solid rgba(255,255,255,0.05); }

        @media (max-width: 992px) {
            .header-topbar-inner { justify-content: center; }
            .center-brand { margin: 0; }
            .logo-img { height: 80px; }
            .header-wrapper.header-solid .logo-img { max-height: 56px; height: auto; }
            .atatr-img { height: 60px; }
            .header-wrapper.header-solid .atatr-img { max-height: 44px; height: auto; }
            .header-solid { border-radius: 0; }
            footer { text-align: center; }
            .footer-title::after { left: 50%; transform: translateX(-50%); }
            .social-links, .footer-contact li { justify-content: center; }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('partials.accessibility')

    <!-- Header: Ana sayfada Hero içinde, diğer sayfalarda burada çağrılır -->
    @if(!Request::is('/'))
        @include('partials.header', ['style' => 'solid'])
    @endif

    <!-- İçerik -->
    @yield('content')

    <!-- Footer -->
    @include('layouts.footer')

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>