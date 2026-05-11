<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borç Sorgulama - T.C. Kırklareli Belediyesi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-2">Borç Sorgulama</h1>
                        <p class="text-muted mb-4">
                            Giriş yapmadan sicil, TCKN, VKN veya abone numarası ile borç sorgulayabilirsiniz.
                        </p>

                        <form id="debt-query-form" class="row g-3" method="post" action="{{ route('e-services.debt-query.submit') }}">
                            @csrf
                            <div class="col-md-4">
                                <label for="mukellef_tipi" class="form-label">Sorgu Tipi</label>
                                <select id="mukellef_tipi" name="mukellef_tipi" class="form-select" required>
                                    <option value="SICIL">Sicil No</option>
                                    <option value="TCKN">T.C. Kimlik No</option>
                                    <option value="VKN">Vergi Kimlik No</option>
                                    <option value="SUABN">Su Abone No</option>
                                    <option value="JEOABN">Jeotermal Abone No</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="mukellef_no" class="form-label">Numara</label>
                                <input
                                    id="mukellef_no"
                                    name="mukellef_no"
                                    type="text"
                                    class="form-control"
                                    maxlength="15"
                                    placeholder="Örn: 12345678901"
                                    required
                                >
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="indirimli_odenecek_mi" name="indirimli_odenecek_mi">
                                    <label class="form-check-label" for="indirimli_odenecek_mi">
                                        İndirimli ödeme seçeneklerini getir
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="sadece_su_borclari" name="sadece_su_borclari">
                                    <label class="form-check-label" for="sadece_su_borclari">
                                        Sadece su borçları
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                @if (config('services.turnstile.enabled') && config('services.turnstile.site_key'))
                                    <div class="cf-turnstile mb-3"
                                         data-sitekey="{{ config('services.turnstile.site_key') }}"
                                         data-callback="onTurnstileSuccess"
                                         data-expired-callback="onTurnstileExpired"></div>
                                    <input type="hidden" id="cf_turnstile_response" name="cf_turnstile_response">
                                @endif
                                <button type="submit" id="debt-query-submit" class="btn btn-primary px-4">
                                    Sorgula
                                </button>
                            </div>
                        </form>

                        <div id="debt-query-alert" class="alert mt-4 d-none"></div>

                        <div id="debt-query-result" class="mt-4 d-none">
                            <h2 class="h5 mb-3">Sorgu Sonucu</h2>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Tahakkuk No</th>
                                            <th>Modül</th>
                                            <th>Son Ödeme</th>
                                            <th class="text-end">Ödenecek Tutar</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody id="debt-table-body"></tbody>
                                </table>
                            </div>
                            @if (config('app.debug'))
                                <details class="mt-3">
                                    <summary>Ham API Yanıtı</summary>
                                    <pre id="debt-query-json" class="bg-light p-3 rounded small mb-0"></pre>
                                </details>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')

@if (config('services.turnstile.enabled') && config('services.turnstile.site_key'))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endif

<script>
    (function () {
        const form = document.getElementById('debt-query-form');
        const submitButton = document.getElementById('debt-query-submit');
        const alertBox = document.getElementById('debt-query-alert');
        const resultBox = document.getElementById('debt-query-result');
        const tableBody = document.getElementById('debt-table-body');
        const rawJson = document.getElementById('debt-query-json');
        const turnstileEnabled = @json((bool) (config('services.turnstile.enabled') && config('services.turnstile.site_key')));
        const turnstileTokenField = document.getElementById('cf_turnstile_response');
        const debtSubmitUrl = @json(route('e-services.debt-query.submit'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        window.onTurnstileSuccess = function (token) {
            if (turnstileTokenField) {
                turnstileTokenField.value = token || '';
            }
        };

        window.onTurnstileExpired = function () {
            if (turnstileTokenField) {
                turnstileTokenField.value = '';
            }
        };

        function setAlert(type, message) {
            alertBox.className = `alert alert-${type} mt-4`;
            alertBox.textContent = message;
            alertBox.classList.remove('d-none');
        }

        function hideAlert() {
            alertBox.classList.add('d-none');
        }

        function findDebtPayload(node) {
            if (!node || typeof node !== 'object') {
                return null;
            }

            if (Object.prototype.hasOwnProperty.call(node, 'tahakkukListesi') ||
                Object.prototype.hasOwnProperty.call(node, 'sonucKodu')) {
                return node;
            }

            for (const key of Object.keys(node)) {
                const child = node[key];
                if (child && typeof child === 'object') {
                    const found = findDebtPayload(child);
                    if (found) {
                        return found;
                    }
                }
            }

            return null;
        }

        function normalizeDebtList(payload) {
            const list = payload?.tahakkukListesi;
            if (!list) {
                return [];
            }

            if (Array.isArray(list)) {
                return list;
            }

            if (Array.isArray(list.item)) {
                return list.item;
            }

            if (list.item && typeof list.item === 'object') {
                return [list.item];
            }

            return [];
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            hideAlert();
            resultBox.classList.add('d-none');
            tableBody.innerHTML = '';

            const payload = {
                mukellef_tipi: form.mukellef_tipi.value,
                mukellef_no: form.mukellef_no.value.trim(),
                indirimli_odenecek_mi: form.indirimli_odenecek_mi.checked,
                sadece_su_borclari: form.sadece_su_borclari.checked,
                cf_turnstile_response: turnstileTokenField ? turnstileTokenField.value : '',
            };

            if (!payload.mukellef_no) {
                setAlert('warning', 'Lütfen sorgu numarasını girin.');
                return;
            }

            if (turnstileEnabled && !payload.cf_turnstile_response) {
                setAlert('warning', 'Lütfen güvenlik doğrulamasını tamamlayın.');
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Sorgulanıyor...';

            try {
                const response = await fetch(debtSubmitUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });

                let result = {};
                try {
                    const text = await response.text();
                    result = text ? JSON.parse(text) : {};
                } catch (parseErr) {
                    setAlert('danger', 'Sunucu yanıtı işlenemedi. Oturumu yenileyip tekrar deneyin.');
                    return;
                }
                if (rawJson) {
                    rawJson.textContent = JSON.stringify(result, null, 2);
                }

                if (response.status === 429 || result.error_code === 'RATE_LIMITED') {
                    const sec = Number(result.retry_after_seconds) || 0;
                    const hint = sec > 0 ? ` (yaklaşık ${Math.ceil(sec / 60)} dk sonra tekrar deneyebilirsiniz.)` : '';
                    setAlert('warning', (result.message || 'Çok fazla istek gönderildi.') + hint);
                    return;
                }

                if (!response.ok || result.status !== 'success') {
                    setAlert('danger', result.message || 'Sorgulama sırasında bir hata oluştu.');
                    return;
                }

                const debtPayload = findDebtPayload(result.data) || result.data || {};
                const debtList = normalizeDebtList(debtPayload);
                const resultCode = String(debtPayload.sonucKodu || '');

                if (resultCode && resultCode !== '1001') {
                    setAlert('warning', debtPayload.sonucAciklamasi || 'Servis yanıtı uyarı döndürdü.');
                } else {
                    setAlert('success', debtPayload.sonucAciklamasi || 'Sorgu başarılı.');
                }

                if (debtList.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-muted">Borç kaydı bulunamadı.</td></tr>';
                } else {
                    debtList.forEach(function (debt) {
                        const row = document.createElement('tr');
                        const active = Number(debt.aktifMi) === 1 ? 'Ödenebilir' : 'Pasif';
                        const cells = [
                            debt.tahakkukNo || '-',
                            debt.modulBilgisi || '-',
                            debt.sonOdemeTarihi || '-',
                            debt.odenecekTutar || '-',
                            active,
                        ];

                        cells.forEach(function (cellValue, index) {
                            const cell = document.createElement('td');
                            if (index === 3) {
                                cell.classList.add('text-end');
                            }
                            cell.textContent = String(cellValue);
                            row.appendChild(cell);
                        });

                        tableBody.appendChild(row);
                    });
                }

                resultBox.classList.remove('d-none');
            } catch (error) {
                setAlert('danger', 'Servise ulaşılamadı. Lütfen daha sonra tekrar deneyin.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Sorgula';
            }
        });
    })();
</script>
</body>
</html>
