    {{-- Vendor CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    {{-- Global Styles --}}
    <style>
        /* =========================
           BASE
        ========================= */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        /* partials/header — iç sayfalar (master dışı şablonlar için) */
        .header-wrapper { position: relative; z-index: 50; }
        .header-wrapper.header-transparent { padding-top: 0; padding-bottom: 2rem; }
        .header-wrapper.header-solid { padding-top: 0; padding-bottom: 0.5rem; }
        .header-transparent { background: linear-gradient(to bottom, rgba(0,0,0,0.9), transparent); }
        .header-solid { background: linear-gradient(90deg, #0052cc, #00c6ff); border-bottom-left-radius: 24px; margin-bottom: 1rem; }
        .header-wrapper.header-solid .logo-img { max-height: 68px !important; height: auto !important; }
        .header-wrapper.header-solid .atatr-img { max-height: 52px !important; height: auto !important; }
        .header-wrapper.header-solid .custom-nav .nav-link { font-size: 0.82rem; padding: 0.35rem 0.75rem; }

        .header-topbar { width: 100%; border-bottom: 1px solid rgba(255, 255, 255, 0.2); }
        .header-wrapper.header-solid .header-topbar { background: rgba(0, 0, 0, 0.2); }
        .header-wrapper.header-transparent .header-topbar { background: rgba(0, 0, 0, 0.4); }
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
        .header-topbar-contact:hover { color: #e0f4ff !important; }
        .header-topbar-contact-label { opacity: 0.92; }
        .header-topbar-sep {
            width: 1px;
            height: 12px;
            background: rgba(255, 255, 255, 0.45);
            flex-shrink: 0;
        }
        .header-topbar-phone { font-weight: 800; font-size: 0.8rem; }
        @media (max-width: 992px) {
            .header-topbar-inner { justify-content: center; }
        }

        /* =========================
           HEADER
        ========================= */
        .detail-header-bg {
            background: linear-gradient(90deg, #0052cc, #00c6ff);
            border-bottom-left-radius: 28px;
            border-bottom-right-radius: 0;
            margin-bottom: 1rem;
            position: relative;
            overflow: visible;
        }

        .header-top-row {
            padding: 0.45rem 0;
        }

        .detail-header-bg .logo-img {
            max-height: 72px;
            width: auto;
            height: auto;
        }

        .detail-logo-container {
            display: flex;
            align-items: center;
        }

        .detail-logo-img {
            max-height: 72px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
        }

        .contact-info {
            color: #fff;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: flex-end;
            margin-bottom: 4px;
        }

        .phone-pill {
            background: #fff;
            color: #1a3c6e;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 800;
        }

        .detail-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            margin-left: 15px;
            transition: color 0.2s;
        }

        .detail-nav .nav-link:hover {
            color: #3498db !important;
        }

        .detail-nav .dropdown-toggle::after {
            display: none;
        }

        .detail-nav .dropdown-menu {
            background: #fff;
            border: none;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            padding: 0.35rem 0;
            min-width: 220px;
        }

        .detail-nav .dropdown-item {
            font-size: 0.85rem;
            font-weight: 600;
            color: #112240;
            padding: 0.45rem 1rem;
            text-transform: none;
        }

        .detail-nav .dropdown-item:hover {
            background: #e8f4ff;
            color: #0052cc;
        }

        /* =========================
           SIDEBAR (Kullanılan sayfalarda)
        ========================= */
        .sidebar-title {
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: #000;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .news-list-item {
            display: block;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid transparent;
        }

        .news-list-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            color: #333;
        }

        .badge-cat {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 800;
            color: #e74c3c;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .side-news-title {
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 5px;
        }

        .side-news-date {
            font-size: 0.75rem;
            color: #999;
        }

        .news-card-default {
            border-left-color: #3498db;
        }

        /* =========================
           CONTENT (Haber/Detay sayfaları vb.)
        ========================= */
        .content-date {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .content-title {
            font-weight: 800;
            font-size: 2rem;
            color: #000;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .content-text {
            font-size: 1.05rem;
            line-height: 1.6;
            color: #444;
            margin-bottom: 2rem;
        }

        .featured-image {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
        }

        /* PDF Butonu */
        .pdf-download-box {
            background-color: #fff0f0;
            border: 1px solid #ffcccc;
            padding: 15px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            text-decoration: none;
            color: #c0392b;
            transition: all 0.3s;
        }

        .pdf-download-box:hover {
            background-color: #ffe5e5;
            color: #a93226;
            transform: translateY(-2px);
        }

        /* =========================
           SIDEBARSIZ SAYFA TASARIMI
        ========================= */
        .page-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            min-height: 60vh;
        }

        .page-title {
            font-weight: 800;
            font-size: 2.5rem;
            color: #1a3c6e;
            text-align: center;
            margin-bottom: 1.5rem;
            position: relative;
        }

        /* .page-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: #00c6ff;
            margin: 15px auto 0;
            border-radius: 2px;
        } */

        .page-featured-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .page-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
        }

        .page-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .page-content-list ul {
            padding-left: 0;
            list-style: none;
        }

        .page-content-list li {
            margin-bottom: 0.75rem;
            padding: 0.75rem 1rem;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #0052cc;
        }

        .page-content-list a {
            color: #112240;
            font-weight: 600;
            text-decoration: none;
        }

        .page-content-list a:hover {
            color: #0052cc;
            text-decoration: underline;
        }

        .breadcrumb-back {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            color: #666;
            text-decoration: none;
            font-weight: 600;
        }

        .breadcrumb-back:hover {
            color: #0052cc;
        }

    /* HEADER (Standart Detay Header) */
    .detail-header-bg {
        background: linear-gradient(90deg, #0052cc, #00c6ff);
        border-bottom-left-radius: 28px;
        border-bottom-right-radius: 0;
        margin-bottom: 1rem;
        position: relative;
        overflow: visible;
    }
    .header-top-row { padding-top: 0.45rem; padding-bottom: 0.45rem; }
    .detail-logo-container { display: flex; align-items: center; }
    .detail-logo-img { max-height: 72px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)); }
    
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
        margin-bottom: 4px;
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

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 992px) {
            .detail-header-bg { border-radius: 0; padding-bottom: 1rem; }
            .detail-logo-container { justify-content: center; margin-bottom: 1rem; }
            .contact-info { justify-content: center; }
            .detail-nav { text-align: center; }
            .content-title { font-size: 1.5rem; }
        }
    /* SİZİN GÖNDERDİĞİNİZ CSS KODLARI */
    .vp-section { margin-bottom: 40px; }
    
    .vp-scroll-container {
        display: flex;
        gap: 15px;
        justify-content: center;
        overflow-x: auto;
        padding: 10px;
        -webkit-overflow-scrolling: touch;
    }

    .vp-card {
        min-width: 150px;
        width: 150px;
        background: white;
        border-radius: 12px;
        text-align: center;
        padding: 15px 5px;
        cursor: pointer;
        border: 2px solid transparent;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
    }

    .vp-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }

    .vp-card.active {
        border-color: #00c6ff;
        background: linear-gradient(to bottom, #ffffff, #f0f8ff);
        box-shadow: 0 10px 25px rgba(0, 198, 255, 0.2);
    }

    .vp-card.active::after {
        content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%);
        width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid #00c6ff;
    }

    .vp-img {
        width: 65px; height: 65px; border-radius: 50%; object-fit: cover;
        margin-bottom: 8px; border: 2px solid #eee;
    }
    .vp-card.active .vp-img { border-color: #00c6ff; }

    .vp-name { font-weight: 700; color: #1a3c6e; font-size: 0.85rem; margin-bottom: 2px; line-height: 1.2; }
    .vp-title { font-size: 0.65rem; color: #777; text-transform: uppercase; font-weight: 600; }

    /* ORGANİZASYON ŞEMASI */
    #orgChartView { display: none; }
    .org-group { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 30px; overflow: hidden; border: 1px solid #eee; }
    .org-header { background: linear-gradient(90deg, #1a3c6e, #2c3e50); padding: 15px 20px; display: flex; align-items: center; color: white; }
    .org-vp-img { width: 60px; height: 60px; border-radius: 50%; border: 3px solid white; object-fit: cover; margin-right: 15px; }
    .org-vp-info h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
    .org-vp-info span { font-size: 0.8rem; opacity: 0.9; font-weight: 300; text-transform: uppercase; }
    .org-list { padding: 0; margin: 0; list-style: none; }
    .org-item { padding: 12px 20px; border-bottom: 1px solid #f1f1f1; display: flex; align-items: center; justify-content: space-between; transition: background 0.2s; cursor: pointer; }
    .org-item:last-child { border-bottom: none; }
    .org-item:hover { background-color: #f9faff; }
    .org-dept-name { font-weight: 600; color: #444; display: flex; align-items: center; gap: 15px; font-size: 0.95rem; }
    .org-dept-icon { width: 35px; height: 35px; background: #eaf2ff; color: #1a3c6e; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    .org-arrow { color: #ccc; transition: transform 0.2s; }
    .org-item:hover .org-arrow { color: #1a3c6e; transform: translateX(5px); }

    /* MÜDÜR KARTI */
    .director-item { transition: all 0.4s ease; }
    .hidden-item { display: none; }
    .fade-in { animation: fadeIn 0.5s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .director-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); height: 100%; border: 1px solid #eee; display: flex; flex-direction: column; cursor: pointer; transition: transform 0.3s ease; }
    .director-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    .card-top-border { height: 5px; background: linear-gradient(90deg, #1a3c6e, #3498db); width: 100%; }
    .director-img-wrapper { height: 220px; overflow: hidden; position: relative; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; }
    .director-img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
    .dept-icon-badge { position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); width: 50px; height: 50px; background: #1a3c6e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; border: 4px solid white; z-index: 10; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .director-info { padding: 35px 20px 20px; text-align: center; flex-grow: 1; display: flex; flex-direction: column; align-items: center; }
    .director-dept { font-size: 0.85rem; font-weight: 800; color: #e74c3c; text-transform: uppercase; margin-bottom: 5px; }
    .director-name { font-weight: 700; font-size: 1.1rem; color: #1a3c6e; margin-bottom: 15px; }
    .btn-detail { margin-top: auto; width: 100%; background: #f8f9fa; color: #1a3c6e; border: 1px solid #eee; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
    .director-card:hover .btn-detail { background: #1a3c6e; color: white; }

    @media (max-width: 992px) {
        .detail-header-bg { border-radius: 0; }
        .detail-logo-container { justify-content: center; margin-bottom: 1rem; }
        .contact-info { justify-content: center; }
        .detail-nav { text-align: center; }
        .vp-scroll-container { justify-content: flex-start; padding-bottom: 15px; }
    }

       

        /* --- YENİ DETAY HEADER CSS --- */
        .detail-header-bg {
            background: linear-gradient(90deg, #0052cc, #00c6ff);
            border-bottom-left-radius: 28px;
            border-bottom-right-radius: 0;
            margin-bottom: 1rem;
            position: relative;
            overflow: visible;
        }

        .header-top-row { padding-top: 0.45rem; padding-bottom: 0.45rem; }

        .detail-logo-container { display: flex; align-items: center; }
        .detail-logo-img { max-height: 72px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3)); }

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
            margin-bottom: 4px;
        }
        .phone-pill {
            background: white;
            color: #1a3c6e;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 800;
        }

        /* --- BAŞKAN SAYFASI İÇERİK CSS --- */
        .mayor-tabs-wrapper {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 30px;
            min-height: 600px;
        }

        .nav-pills .nav-link {
            color: #555;
            font-weight: 700;
            text-transform: uppercase;
            padding: 15px 25px;
            border-radius: 50px;
            margin-right: 10px;
            margin-bottom: 10px;
            background-color: #f1f1f1;
            transition: all 0.3s;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(90deg, #0052cc, #00c6ff);
            color: white;
            box-shadow: 0 5px 15px rgba(0, 82, 204, 0.3);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #e2e6ea;
            color: #0052cc;
        }

        .tab-content { padding: 30px 10px; }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1a3c6e;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: #e74c3c;
            margin-top: 10px;
            border-radius: 2px;
        }

        .text-content { font-size: 1.05rem; line-height: 1.7; color: #444; }

        .mayor-bio-img {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            border: 5px solid white;
            /* Resim oranını korumak için */
            object-fit: cover;
            aspect-ratio: 3/4; 
        }

        .past-mayors-content .past-mayors-grid,
        .page-content .past-mayors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
        }

        .past-mayors-content .past-mayor-chip,
        .page-content .past-mayor-chip {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 56px;
            padding: 14px 16px;
            border: 1px solid #c8d9ef;
            border-radius: 12px;
            background: #f8fbff;
            color: #1a3c6e;
            font-weight: 700;
            font-size: 0.88rem;
            line-height: 1.35;
            text-align: center;
        }

        /* Form */
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .form-control:focus {
            border-color: #0052cc;
            box-shadow: 0 0 0 0.25rem rgba(0, 82, 204, 0.15);
            background-color: white;
        }
        .btn-submit {
            background: linear-gradient(90deg, #0052cc, #00c6ff);
            color: white;
            padding: 12px 40px;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            transition: transform 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .message-box {
            border-left: 5px solid #e74c3c;
            background: #fff5f5;
            padding: 30px;
            border-radius: 0 15px 15px 0;
            font-style: italic;
            font-size: 1.1rem;
            color: #555;
        }

        @media (max-width: 992px) {
            .detail-header-bg { border-radius: 0; padding-bottom: 1rem; }
            .detail-logo-container { justify-content: center; margin-bottom: 1rem; }
            .contact-info { justify-content: center; }
            .detail-nav { text-align: center; }
            .nav-pills { justify-content: center; }
            .mayor-bio-img { margin-top: 20px; }
        }


        /* CARD TASARIMI */
        .member-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 1px solid #eee;
            height: 100%;
            position: relative;
        }
        
        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 82, 204, 0.15);
            border-color: #00c6ff;
        }

        .member-img-container {
            width: 100%;
            aspect-ratio: 3/4; /* Vesikalık formatı */
            overflow: hidden;
            background-color: #f1f1f1;
            position: relative;
        }

        .member-img {
            width: 100%;
            height: 100%;
            
            object-fit: cover;
            transition: transform 0.5s;
        }

        .member-card:hover .member-img {
            transform: scale(1.05);
        }

        .member-info {
            padding: 20px;
            text-align: center;
            background: white;
            position: relative;
            z-index: 2;
        }

        .member-name {
            font-weight: 700;
            color: #1a3c6e;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .member-title {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Parti Rozeti */
        .party-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
            background-color: #999; /* Varsayılan renk */
        }

        /* Parti Renkleri (Otomatik Class Ataması İçin) */
        .party-ak, .party-ak-parti { background-color: #ff9d1e; }
        .party-chp { background-color: #e30000; }
        .party-mhp { background-color: #830000; }
        .party-iyi, .party-iyi-parti { background-color: #00a0df; }
        .party-bagimsiz { background-color: #555; }

        /* Dekoratif Çizgi */
        .card-divider {
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #0052cc, #00c6ff);
            margin: 0 auto 10px auto;
            border-radius: 2px;
        }

        /* Sosyal Medya (Opsiyonel) */
        .member-social {
            margin-top: 10px;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s;
        }
        .member-card:hover .member-social {
            opacity: 1;
            transform: translateY(0);
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
            margin-bottom: 3rem;
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

        /* --- GRUP BAŞLIĞI --- */
        .party-group-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            margin-top: 1rem;
        }
        .party-group-title {
            font-weight: 800;
            font-size: 1.5rem;
            color: #1a3c6e;
            text-transform: uppercase;
            padding-right: 15px;
            white-space: nowrap;
        }
        .party-group-line {
            flex-grow: 1;
            height: 2px;
            background-color: #e0e0e0;
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

        /* Parti Rozeti */
        .party-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            text-transform: uppercase;
            z-index: 10;
        }
        
        .badge-baskan { color: #1a3c6e; border-left: 3px solid #1a3c6e; }
        .badge-default { color: #555; border-left: 3px solid #555;}

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
    

        <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        /* --- HEADER STİLLERİ (Site Geneli Uyumlu) --- */
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

        /* --- SAYFA ÖZEL STİLLERİ (Sizin Tasarımınız) --- */
        
        /* YIL SEKMELERİ */
        .year-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .year-tab-btn {
            padding: 10px 25px;
            border-radius: 50px;
            background: white;
            border: 1px solid #ddd;
            color: #555;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        .year-tab-btn:hover, .year-tab-btn.active {
            background: #1a3c6e;
            color: white;
            border-color: #1a3c6e;
            box-shadow: 0 5px 15px rgba(26, 60, 110, 0.3);
        }

        /* ACCORDION TASARIMI */
        .accordion-item {
            border: none;
            margin-bottom: 15px;
            border-radius: 10px !important;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .accordion-button {
            background-color: #fff;
            color: #1a3c6e;
            font-weight: 700;
            padding: 20px;
            font-size: 1.1rem;
            border-left: 5px solid #1a3c6e;
        }
        
        .accordion-button:not(.collapsed) {
            background-color: #f8faff;
            color: #1a3c6e;
            box-shadow: none;
        }
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }

        /* İÇERİK BUTONLARI */
        .decision-files-row {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            padding: 20px;
        }

        .file-card {
            flex: 1;
            min-width: 200px;
            background: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #555;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .file-card:hover {
            transform: translateY(-5px);
            background: #fff;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: #3498db;
        }

        .file-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .file-gundem .file-icon { color: #3498db; }
        .file-karar .file-icon { color: #27ae60; }
        .file-komisyon .file-icon { color: #e67e22; }

        .file-title {
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .disabled-file {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        @media (max-width: 992px) {
            .internal-header { text-align: center; border-radius: 0; }
            .internal-nav { justify-content: center !important; margin-top: 15px; }
            .decision-files-row { flex-direction: column; }
            .file-card { width: 100%; }
        }
    
 

        /* --- HEADER (Site Geneli) --- */
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
        .page-title {
            font-weight: 800;
            color: #1a3c6e;
            margin-bottom: 10px;
        }
        .title-divider {
            width: 60px;
            height: 4px;
            background: #e74c3c;
            margin-bottom: 20px;
            border-radius: 2px;
        }

        /* --- İLETİŞİM KARTLARI --- */
        .contact-info-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
            border: 1px solid #eee;
            transition: transform 0.3s;
        }
        
        .contact-info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background: #f0f4ff;
            color: #1a3c6e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .info-content h5 {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .info-content p {
            color: #666;
            margin: 0;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .info-content a {
            color: #1a3c6e;
            text-decoration: none;
            font-weight: 500;
        }

        /* --- HARİTA --- */
        .map-container {
            width: 100%;
            height: 450px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 5px solid white;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .internal-header { text-align: center; border-radius: 0; }
            .internal-nav { justify-content: center !important; margin-top: 15px; }
        }
    
    </style>

    <style>
        /* Header menu readability override */
        .custom-nav .nav-link,
        .detail-nav .nav-link {
            color: #ffffff !important;
            opacity: 1 !important;
            text-transform: capitalize !important;
        }

        .custom-nav .nav-link:hover,
        .custom-nav .nav-link:focus,
        .custom-nav .nav-link.active,
        .detail-nav .nav-link:hover,
        .detail-nav .nav-link:focus,
        .detail-nav .nav-link.active {
            color: #ffffff !important;
        }

        .custom-nav .dropdown-toggle::after,
        .detail-nav .dropdown-toggle::after {
            display: none;
        }
    </style>

    {{-- Page specific styles --}}
    @stack('styles')
