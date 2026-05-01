<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Hizmet Başvuru ve Evrak Sorgu - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container py-5">
        <div class="mb-4">
            <h1 class="mb-2">E-Hizmet Başvuru ve Evrak Sorgulama</h1>
            <p class="text-muted mb-0">
                Ruhsat, E-İmar, Evrak ve Sosyal Destek başvurularını buradan oluşturabilir; takip numarasıyla durum sorgulayabilirsiniz.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Yeni E-Hizmet Başvurusu</h2>
                        <div id="application-message" class="alert d-none" role="alert"></div>

                        <form id="application-form" class="row g-3">
                            <input type="hidden" name="source" value="e-hizmet-basvuru-sayfasi">
                            <input type="hidden" name="platform" value="web">

                            <div class="col-md-6">
                                <label class="form-label" for="service_type">Hizmet Türü</label>
                                <select id="service_type" name="service_type" class="form-select" required>
                                    <option value="ruhsat">Ruhsat</option>
                                    <option value="e_imar">E-İmar</option>
                                    <option value="evrak">Evrak Takibi / Doğrulama</option>
                                    <option value="sosyal_destek">Sosyal Destek</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="full_name">Ad Soyad</label>
                                <input id="full_name" name="full_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="identity_no">T.C. Kimlik No</label>
                                <input id="identity_no" name="identity_no" class="form-control" maxlength="11">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="phone">Telefon</label>
                                <input id="phone" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">E-Posta</label>
                                <input id="email" type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="address">Adres</label>
                                <input id="address" name="address" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="request_summary">Başvuru Özeti</label>
                                <textarea id="request_summary" name="request_summary" rows="5" class="form-control" required></textarea>
                            </div>
                            <div class="col-12">
                                <button id="application-submit-btn" class="btn btn-primary" type="submit">Başvuru Gönder</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Evrak / Başvuru Sorgu</h2>
                        <div id="track-message" class="alert d-none" role="alert"></div>

                        <form id="application-track-form" class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="track_no">Takip No</label>
                                <input id="track_no" name="track_no" class="form-control" placeholder="EB-20260501-XXXXXX" required>
                            </div>
                            <div class="col-12">
                                <button id="track-btn" class="btn btn-outline-primary" type="submit">Sorgula</button>
                            </div>
                        </form>

                        <div id="track-card" class="mt-4 d-none">
                            <h3 class="h6 mb-3">Sonuç</h3>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Takip No:</strong> <span id="r_tracking">-</span></li>
                                <li class="list-group-item"><strong>Tür:</strong> <span id="r_type">-</span></li>
                                <li class="list-group-item"><strong>Ad Soyad:</strong> <span id="r_name">-</span></li>
                                <li class="list-group-item"><strong>Durum:</strong> <span id="r_status">-</span></li>
                                <li class="list-group-item"><strong>Dönüş:</strong> <span id="r_response">Henüz dönüş yapılmadı.</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

<script>
    const appForm = document.getElementById('application-form');
    const appMessage = document.getElementById('application-message');
    const appBtn = document.getElementById('application-submit-btn');

    const trackForm = document.getElementById('application-track-form');
    const trackMessage = document.getElementById('track-message');
    const trackBtn = document.getElementById('track-btn');
    const trackCard = document.getElementById('track-card');

    function setAlert(target, type, text) {
        target.classList.remove('d-none', 'alert-success', 'alert-danger');
        target.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        target.textContent = text;
    }

    function typeLabel(type) {
        switch (type) {
            case 'ruhsat': return 'Ruhsat';
            case 'e_imar': return 'E-İmar';
            case 'evrak': return 'Evrak';
            case 'sosyal_destek': return 'Sosyal Destek';
            default: return type || '-';
        }
    }

    function statusLabel(status) {
        switch (status) {
            case 'received': return 'Alındı';
            case 'in_process': return 'İşlemde';
            case 'completed': return 'Tamamlandı';
            case 'rejected': return 'Reddedildi';
            default: return status || '-';
        }
    }

    appForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        appBtn.disabled = true;
        appBtn.textContent = 'Gönderiliyor...';
        appMessage.classList.add('d-none');

        const payload = Object.fromEntries(new FormData(appForm).entries());

        try {
            const response = await fetch('/api/citizen-applications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Platform': 'web'
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();
            if (! response.ok) {
                throw new Error(result.message || 'Başvuru oluşturulamadı.');
            }

            setAlert(appMessage, 'success', `Başvurunuz alındı. Takip numaranız: ${result.tracking_no}`);
            appForm.reset();
        } catch (error) {
            setAlert(appMessage, 'error', error.message || 'Bir hata oluştu.');
        } finally {
            appBtn.disabled = false;
            appBtn.textContent = 'Başvuru Gönder';
        }
    });

    trackForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        trackBtn.disabled = true;
        trackBtn.textContent = 'Sorgulanıyor...';
        trackMessage.classList.add('d-none');
        trackCard.classList.add('d-none');

        const trackingNo = document.getElementById('track_no').value.trim();

        try {
            const response = await fetch(`/api/citizen-applications/${encodeURIComponent(trackingNo)}`, {
                headers: { 'Accept': 'application/json' },
            });

            const result = await response.json();
            if (! response.ok) {
                throw new Error(result.message || 'Kayıt bulunamadı.');
            }

            document.getElementById('r_tracking').textContent = result.data.tracking_no || '-';
            document.getElementById('r_type').textContent = typeLabel(result.data.service_type);
            document.getElementById('r_name').textContent = result.data.full_name || '-';
            document.getElementById('r_status').textContent = statusLabel(result.data.status);
            document.getElementById('r_response').textContent = result.data.response || 'Henüz dönüş yapılmadı.';

            trackCard.classList.remove('d-none');
        } catch (error) {
            setAlert(trackMessage, 'error', error.message || 'Bir hata oluştu.');
        } finally {
            trackBtn.disabled = false;
            trackBtn.textContent = 'Sorgula';
        }
    });
</script>
</body>
</html>
