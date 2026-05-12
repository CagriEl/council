<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borç Sorgulama — Veri İşleme ve Loglama - T.C. Kırklareli Belediyesi</title>
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
                    <span class="text-dark">Borç sorgulama veri işleme</span>
                </nav>
                <h1 class="h2 mb-3">Borç sorgulama — kişisel veri işleme ve loglama politikası</h1>
                <p class="text-muted small mb-4">
                    Bu sayfa, e-belediye borç sorgulama ekranında toplanan verilerin türü, amacı ve denetim kayıtlarının tutulması hakkında özet bilgi verir.
                    Metin örnektir; hukuki onay için kurum avukatlığı önerilir.
                </p>

                <div class="content-card p-4 p-md-5 mb-4">
                    <h2 class="h5 text-primary">Amaç</h2>
                    <p class="content-text mb-0">
                        Kamu hizmeti kapsamında borç bilgisinin sorgulanması, kötüye kullanımın (otomatik sorgu, bot) sınırlandırılması, bilgi güvenliği olaylarının incelenmesi
                        ve yasal yükümlülüklerin yerine getirilmesi amacıyla sınırlı veri işlenir ve denetim kaydı oluşturulur.
                    </p>
                </div>

                <div class="content-card p-4 p-md-5 mb-4">
                    <h2 class="h5 text-primary">İşlenen / kaydedilen veriler (özet)</h2>
                    <ul class="content-text mb-0">
                        <li><strong>Teknik:</strong> istek kimliği (UUID), IP adresi, tarayıcı bilgisi (User-Agent).</li>
                        <li><strong>Sorgu:</strong> mükellef tipi; mükellef numarası veritabanında <strong>maskelenmiş</strong> formatta (ör. T.C. kimlik no kısmen gizlenir).</li>
                        <li><strong>Güvenlik:</strong> güvenlik doğrulaması (ör. Cloudflare Turnstile) başarı durumu.</li>
                        <li><strong>İşlem:</strong> süre (ms), uç servis sonuç kodu (varsa), deneme sonucu (başarı, doğrulama hatası, limit vb.).</li>
                    </ul>
                    <p class="content-text mt-3 mb-0 small text-muted">
                        Yukarıdaki kayıtlar <code>debt_query_audits</code> tablosu ve yapılandırılmış günlük kanalları üzerinden tutulabilir.
                    </p>
                </div>

                <div class="content-card p-4 p-md-5 mb-4">
                    <h2 class="h5 text-primary">Saklama süresi</h2>
                    <p class="content-text mb-0">
                        Denetim kayıtları, yapılandırmada tanımlı süre boyunca saklanır. Güncel hedef süre:
                        <strong>{{ (int) config('services.e_odeme.audit_retention_days', 365) }} gün</strong>
                        (yönetici <code>E_ODEME_AUDIT_RETENTION_DAYS</code> ile değiştirebilir). Süre dolduğunda kayıtların silinmesi için sistem tarafında
                        periyodik arşivleme/silme işlemi tanımlanmalıdır (otomatik job bu repoda isteğe bağlıdır).
                    </p>
                </div>

                <div class="content-card p-4 p-md-5 mb-4">
                    <h2 class="h5 text-primary">Üçüncü taraflar</h2>
                    <p class="content-text mb-0">
                        Borç bilgisi, belediye ile anlaşmalı ödeme / tahakkuk sağlayıcısının (ör. BELSIS SOAP) sistemlerine iletilir. Bu aktarımın hukuki dayanağı ve sınırları
                        ilgili sözleşme ve KVKK aydınlatma metinleri çerçevesinde değerlendirilir. Sorgu öncesi
                        <a href="{{ route('legal.kvkk') }}">KVKK aydınlatma metni</a> ve aşağıdaki borç sorgulama ekranındaki onay kutusu ile bilgilendirme yapılır.
                    </p>
                </div>

                <p class="small text-muted mb-0">
                    <a href="{{ route('e-services.debt-query') }}">Borç sorgulama ekranına dön</a>
                    · <a href="{{ route('legal.kvkk') }}">KVKK</a>
                </p>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
</body>
</html>
