<style>
       footer {
            background-color: #112240;
            color: #fff;
            padding-top: 70px;
            position: relative;
            font-size: 0.9rem;
        }
        .footer-logo {
            height: 100px;
            margin-bottom: 20px;
            filter: brightness(0) invert(1); /* Logoyu beyaz yap */
        }
        .footer-desc {
            opacity: 0.7;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .footer-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }
        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; width: 40px; height: 3px;
            background: #00c6ff;
        }
        
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer-links a:hover { color: #00c6ff; padding-left: 5px; }
        .footer-links i { font-size: 0.8rem; }

        .footer-contact li {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            color: rgba(255,255,255,0.8);
        }
        .footer-contact i {
            font-size: 1.2rem;
            color: #00c6ff;
            margin-top: 3px;
        }

        .social-links { display: flex; gap: 10px; margin-top: 20px; }
        .social-link {
            width: 40px; height: 40px; background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; color: white; text-decoration: none; transition: 0.3s;
        }
        .social-link:hover { background: #00c6ff; transform: translateY(-3px); }

        .copyright {
            background: #0a162b;
            padding: 20px 0;
            margin-top: 50px;
            text-align: center;
            font-size: 0.85rem;
            opacity: 0.6;
            border-top: 1px solid rgba(255,255,255,0.05);
        }
</style>
<footer>
        <div class="container">
            <div class="row">
                <!-- Footer Logo & Hakkında -->
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img"> &nbsp;
                     <p class="footer-desc">
                        Kırklareli Belediyesi, şeffaf, katılımcı ve yenilikçi belediyecilik anlayışıyla şehrimize değer katmaya devam ediyor. Tarihi dokusu, kültürel zenginliği ve modern yüzüyle Kırklareli hepimizin.
                    </p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/kirklarelibelediyesi" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://x.com/Kirkbelbasin" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/kirklarelibelediyesi" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="{{ route('iletisim') }}" class="social-link" aria-label="İletişim"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-title">KURUMSAL</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('baskan') }}"><i class="fas fa-chevron-right"></i> Başkan</a></li>
                        <li><a href="{{ route('meclis') }}"><i class="fas fa-chevron-right"></i> Meclis Üyeleri</a></li>
                        <li><a href="{{ route('mudurler') }}"><i class="fas fa-chevron-right"></i> Organizasyon Şeması</a></li>
                        <li><a href="{{ route('strategic-plans.index') }}"><i class="fas fa-chevron-right"></i> Stratejik Plan</a></li>
                        <li><a href="{{ route('activity-reports.index') }}"><i class="fas fa-chevron-right"></i> Faaliyet Raporları</a></li>
                        <li><a href="{{ route('rehber') }}"><i class="fas fa-chevron-right"></i> Telefon Rehberi</a></li>
                    </ul>
                </div>

                <!-- Online İşlemler -->
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-title">ONLINE İŞLEMLER</h5>
                    <ul class="footer-links">
                        <li>
                            <a href="https://e-belediye.kirklareli.bel.tr" target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-chevron-right"></i> E-Belediye Portalı
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('iletisim') }}">
                                <i class="fas fa-chevron-right"></i> Talep / Şikayet
                            </a>
                        </li>
                        <li>
                            <a href="https://e-belediye.kirklareli.bel.tr" target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-chevron-right"></i> Vefat İlanları
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- İletişim -->
                <div class="col-lg-4 col-md-4">
                    <h5 class="footer-title">İLETİŞİM</h5>
                    <ul class="footer-links footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Karakaş Mahallesi, Cumhuriyet Meydanı No:1, 39000 Merkez/Kırklareli</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <span>444 01 39</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>iletisim@kirklareli.bel.tr</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        &copy; 2026 T.C. Kırklareli Belediyesi. Tüm Hakları Saklıdır.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        Gizlilik Politikası | Kullanım Şartları
                    </div>
                </div>
            </div>
        </div>
    </footer>