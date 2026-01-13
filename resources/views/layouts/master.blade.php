<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'T.C. Kırklareli Belediyesi')</title>

    <!-- CSS Kütüphaneleri -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- Genel Stiller -->
    <style>
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; background-color: #fff; display: flex; flex-direction: column; min-height: 100vh; }
        
        /* HEADER & NAV STİLLERİ (Ortak) */
        .header-wrapper { padding-top: 1rem; padding-bottom: 2rem; position: relative; z-index: 50; }
        .header-transparent { background: linear-gradient(to bottom, rgba(0,0,0,0.9), transparent); }
        .header-solid { background: linear-gradient(90deg, #0052cc, #00c6ff); border-bottom-left-radius: 50px; margin-bottom: 3rem; }
        
        .contact-badge-wrapper { position: absolute; top: 25px; right: 40px; z-index: 60; display: flex; align-items: center; gap: 15px; }
        .weather-badge { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 6px 20px; border-radius: 30px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; text-decoration: none; transition: all 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .weather-badge:hover { background: white; color: #0052cc; transform: translateY(-2px); }
        .contact-badge { background: linear-gradient(90deg, #0052cc, #00c6ff); color: white; padding: 6px 24px; border-radius: 30px; font-weight: 700; display: inline-flex; align-items: center; gap: 10px; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2); font-size: 0.9rem; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        
        .center-brand { display: flex; align-items: center; justify-content: center; margin: 0 20px; transition: transform 0.3s; }
        .center-brand:hover { transform: scale(1.05); }
        .logo-img { max-height: 120px; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5)); }
        .atatr-img { max-height: 80px; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5)); margin-left: 10px; }
        
        .custom-nav .nav-link { color: white !important; font-weight: 700; font-size: 1rem; text-transform: uppercase; padding: 0.5rem 1.2rem; margin: 0 2px; transition: all 0.3s; text-shadow: 0 2px 4px rgba(0,0,0,0.8); position: relative; }
        .custom-nav .nav-link::after { content: ''; display: block; width: 0; height: 2px; background: #00c6ff; transition: width .3s; margin: 0 auto; }
        .custom-nav .nav-link:hover::after { width: 100%; }
        .custom-nav .nav-link:hover { color: #00c6ff !important; }

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
            .contact-badge-wrapper { display: none; } 
            .center-brand { margin: 0; }
            .logo-img { height: 80px; }
            .atatr-img { height: 60px; }
            .header-solid { border-radius: 0; }
            footer { text-align: center; }
            .footer-title::after { left: 50%; transform: translateX(-50%); }
            .social-links, .footer-contact li { justify-content: center; }
        }
    </style>

    @stack('styles')
</head>
<body>

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