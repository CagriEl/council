<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim - T.C. Kırklareli Belediyesi</title>
         @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container mb-5">
        
        <div class="row g-5">
            <!-- SOL KOLON: İLETİŞİM BİLGİLERİ -->
            <div class="col-lg-5">
                <div class="contact-info-card">
                    <h2 class="page-title">İletişim Bilgileri</h2>
                    <div class="title-divider"></div>
                    <p class="text-muted mb-4">Her türlü soru, öneri ve talepleriniz için aşağıdaki iletişim kanallarından veya yandaki formu kullanarak bize ulaşabilirsiniz.</p>
                    <div class="alert alert-light border small mb-0" role="note">
                        <strong>Talep / şikayet kaydı ve takip:</strong>
                        Resmî kayıt numarası almak ve durum sorgulamak için
                        <a href="{{ route('service-requests.page') }}">Talep / Şikayet</a> sayfasını kullanın.
                        Kişisel veriler: <a href="{{ route('legal.kvkk') }}">KVKK</a>.
                    </div>

                    <!-- Adres -->
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-content">
                            <h5>Adres</h5>
                            <p>Karakaş Mahallesi, Zincirlikuyu Caddesi<br>No: 12, 39000 Merkez / Kırklareli</p>
                        </div>
                    </div>

                    <!-- Telefon -->
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="info-content">
                            <h5>Telefon / Çözüm Merkezi</h5>
                            <p><a href="tel:4440139">444 01 39</a></p>
                            <p><a href="tel:02882141045">0 (288) 214 10 45</a></p>
                        </div>
                    </div>

                    <!-- E-Posta -->
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="info-content">
                            <h5>E-Posta</h5>
                            <p><a href="mailto:iletisim@kirklareli.bel.tr">kirklareli@kirklareli.bel.tr</a></p>
                            <p><a href="mailto:baskan@kirklareli.bel.tr">baskan@kirklareli.bel.tr</a></p>
                        </div>
                    </div>

                    <!-- Çalışma Saatleri -->
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-clock"></i></div>
                        <div class="info-content">
                            <h5>Çalışma Saatleri</h5>
                            <p>Hafta İçi: 08:30 - 17:30</p>
                            <p class="text-danger small">Hafta sonu kapalıdır.</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- SAĞ KOLON: İLETİŞİM FORMU (Bileşen) -->
            <div class="col-lg-7">
                <!-- 
                    Daha önce oluşturduğumuz 'api-form' bileşenini kullanıyoruz.
                    code="iletisim-sayfasi" diyerek panelde bu formun kaynağını ayırt ediyoruz.
                -->
                <x-contact-form source="iletisim-sayfasi" :enable-tracking="true" />
            </div>
        </div>

        <!-- HARİTA BÖLÜMÜ -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3007.037920364239!2d27.22324331541662!3d41.73752897923364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40a067cfce2830d1%3A0x6e3c35b9b7a3740!2sK%C4%B1rklareli%20Belediyesi!5e0!3m2!1str!2str!4v1675245678901!5m2!1str!2str" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>

    </div>
</main>

    <!-- Bootstrap JS -->
    @include('layouts.footer')
</body>
</html>