<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Belediye - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container py-5">
        <div class="mb-4">
            <h1 class="mb-2">E-Belediye / Online İşlemler</h1>
            <p class="text-muted mb-0">
                Sık kullanılan belediye hizmetlerine buradan hızlıca erişebilirsiniz.
                Dijital olarak henüz açılmayan hizmetler için bilgi ekranına yönlendirilirsiniz.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <a class="text-decoration-none d-block h-100" href="{{ route('e-services.debt-query') }}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">Borç Sorgulama</h5>
                            <p class="card-text text-muted mb-0">Emlak, çevre temizlik ve diğer borç kalemleri için sorgulama ekranı.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a class="text-decoration-none d-block h-100" href="{{ route('e-services.debt-query') }}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">Online Tahsilat</h5>
                            <p class="card-text text-muted mb-0">Kart ile güvenli ödeme adımlarına erişim.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a class="text-decoration-none d-block h-100" href="{{ route('service-requests.page') }}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">Talep / Şikayet</h5>
                            <p class="card-text text-muted mb-0">İstek, öneri ve şikayetlerinizi kayıt altına alın ve durumunu takip edin.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a class="text-decoration-none d-block h-100" href="https://www.turkiye.gov.tr/saglik-titck-nobetci-eczane-sorgulama" target="_blank" rel="noopener noreferrer">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">Nöbetçi Eczaneler</h5>
                            <p class="card-text text-muted mb-0">Güncel nöbetçi eczane bilgilerine erişim.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a class="text-decoration-none d-block h-100" href="{{ route('obituaries.public') }}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">Vefat İlanları</h5>
                            <p class="card-text text-muted mb-0">Vefat kaydı duyuruları ve defin bilgileri.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a class="text-decoration-none d-block h-100" href="https://www.turkiye.gov.tr/belediyeler" target="_blank" rel="noopener noreferrer">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">e-Devlet Belediye Hizmetleri</h5>
                            <p class="card-text text-muted mb-0">e-Devlet üzerindeki belediye hizmetlerine doğrudan erişim.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
</body>
</html>
