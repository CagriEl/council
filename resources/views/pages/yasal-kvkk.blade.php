<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kişisel Verilerin Korunması (KVKK) - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav class="mb-3 small text-muted" aria-label="Sayfa yolu">
                    <a href="{{ route('home') }}">Ana Sayfa</a>
                    <span class="mx-1">/</span>
                    <span>Yasal</span>
                    <span class="mx-1">/</span>
                    <span class="text-dark">KVKK</span>
                </nav>
                <h1 class="h2 mb-3">6698 Sayılı Kişisel Verilerin Korunması Kanunu (KVKK) Aydınlatma Metni</h1>
                <p class="text-muted small mb-4">Son güncelleme: {{ now()->translatedFormat('d F Y') }}. Metin örnektir; kesin hukuki metin için kurum avukatlığı onayı önerilir.</p>

                <div class="content-card p-4 p-md-5 mb-4">
                    <h2 class="h5 text-primary">Veri sorumlusu</h2>
                    <p class="content-text mb-0">
                        T.C. Kırklareli Belediyesi, 6698 sayılı Kanun kapsamında veri sorumlusudur. İletişim için
                        <a href="{{ route('iletisim') }}">İletişim</a> sayfasındaki kanallar kullanılabilir.
                    </p>
                </div>

                <div class="content-card p-4 p-md-5 mb-4">
                    <h2 class="h5 text-primary">İşlenen veri kategorileri (örnek)</h2>
                    <ul class="content-text mb-0">
                        <li>Web sitesi ve mobil uygulama: kimlik/iletişim (ad-soyad, telefon, e-posta), talep içeriği, teknik log (IP, tarayıcı bilgisi).</li>
                        <li>Talep / şikayet başvuruları: başvuru formunda ilettiğiniz tüm alanlar ve takip için oluşturulan kayıtlar.</li>
                        <li>Borç sorgulama: sorgu tipi, maskelenmiş tanımlayıcı özetleri, güvenlik (captcha) sonucu, süre ve durum bilgisi (ayrıntılı açıklama için
                            <a href="{{ route('legal.debt-query-processing') }}">Borç sorgulama ve loglama</a> sayfasına bakınız).</li>
                    </ul>
                </div>

                <div class="content-card p-4 p-md-5 mb-4">
                    <h2 class="h5 text-primary">İşleme amaçları ve hukuki sebepler</h2>
                    <p class="content-text mb-0">
                        Hizmet sunumu, talebinizi işleme alma, yasal yükümlülüklerin yerine getirilmesi, bilgi güvenliği ve kötüye kullanımın önlenmesi
                        amaçlarıyla; KVKK m.5 ve m.6 kapsamında ilgili şartlara dayanılarak işlenir.
                    </p>
                </div>

                <div class="content-card p-4 p-md-5 mb-4">
                    <h2 class="h5 text-primary">Haklarınız</h2>
                    <p class="content-text mb-3">
                        Kanunun 11. maddesi uyarınca; verilerinizin işlenip işlenmediğini öğrenme, işlenmişse bilgi talep etme, işlenme amacına uygun kullanılıp kullanılmadığını öğrenme,
                        yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme, eksik veya yanlış işlenmişse düzeltilmesini isteme, silinmesini veya yok edilmesini isteme,
                        aktarılan üçüncü kişilere bildirilmesini isteme, münhasıran otomatik sistemler ile analiz edilmesi suretiyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme
                        ve kanuna aykırı işlenmesi sebebiyle zararın giderilmesini talep etme haklarına sahipsiniz.
                    </p>
                    <p class="content-text mb-0">
                        Başvurularınızı yazılı veya Kurul’un belirleyeceği diğer yöntemlerle iletebilirsiniz. Aşağıdaki form ile de kayıt oluşturabilirsiniz; konu olarak
                        <strong>KVKK başvurusu</strong> seçmeniz işleminizi hızlandırır.
                    </p>
                </div>

                <div class="mb-4">
                    <x-contact-form
                        source="kvkk-sayfasi"
                        title="KVKK kapsamındaki başvuru formu"
                        :enable-tracking="true"
                    />
                </div>

                <p class="small text-muted mb-0">
                    İlgili: <a href="{{ route('legal.debt-query-processing') }}">Borç sorgulama veri işleme ve loglama</a>
                    · <a href="{{ route('service-requests.page') }}">Talep / şikayet</a>
                    · <a href="{{ route('iletisim') }}">İletişim</a>
                </p>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
</body>
</html>
