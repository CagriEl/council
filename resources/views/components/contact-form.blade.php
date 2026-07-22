@props(['source' => 'genel', 'title' => null])

@php
    /** @var \App\Services\CloudflareTurnstile $turnstile */
    $turnstile = app(\App\Services\CloudflareTurnstile::class);
    $turnstileSiteKey = $turnstile->siteKey();
    $turnstileEnabled = $turnstile->enabled();
@endphp

<div class="contact-form-container" data-turnstile="{{ $turnstileEnabled ? '1' : '0' }}">
    @if($title)
        <h3 class="mb-4 fw-bold text-dark border-bottom pb-2">{{ $title }}</h3>
    @endif

    <div class="form-message alert d-none" role="alert"></div>

    <form action="{{ url('api/contact/submit') }}" onsubmit="submitContactForm(event)" class="row g-3 p-4 border rounded shadow-sm bg-white">

        <input type="hidden" name="source" value="{{ $source }}">
        <input type="hidden" name="platform" value="web">

        {{-- Honeypot --}}
        <div class="d-none" aria-hidden="true">
            <label for="company_url_{{ $source }}">Website</label>
            <input type="text" name="company_url" id="company_url_{{ $source }}" value="" tabindex="-1" autocomplete="off">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Adınız Soyadınız</label>
            <input type="text" name="name" class="form-control" placeholder="Adınız Soyadınız" required minlength="2" maxlength="120">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Telefon Numaranız</label>
            <input type="tel" name="phone" class="form-control" placeholder="05XX XXX XX XX" maxlength="30">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">E-Posta Adresiniz</label>
            <input type="email" name="email" class="form-control" placeholder="ornek@mail.com" maxlength="190">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Konu</label>
            <select name="subject" class="form-select">
                <option selected>Genel İstek / Öneri</option>
                <option>Park ve Bahçeler</option>
                <option>Fen İşleri / Yol</option>
                <option>Temizlik İşleri</option>
                <option>Zabıta / Şikayet</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label fw-bold">Mesajınız</label>
            <textarea name="message" class="form-control" rows="5" placeholder="Mesajınızı buraya yazınız..." required minlength="5" maxlength="5000"></textarea>
        </div>

        @if($turnstileEnabled && $turnstileSiteKey)
            <div class="col-12">
                <label class="form-label fw-bold d-block text-center mb-2">Güvenlik doğrulaması</label>
                <div class="d-flex justify-content-center">
                    {{-- Implicit render: class=cf-turnstile + data-sitekey --}}
                    <div
                        class="cf-turnstile"
                        data-sitekey="{{ $turnstileSiteKey }}"
                        data-theme="light"
                        data-language="tr"
                        data-size="normal"
                        data-appearance="always"
                    ></div>
                </div>
                <p class="text-center text-muted small mt-2 mb-0" id="kb-turnstile-hint-{{ $source }}">
                    Kutucuk görünmezse sayfayı yenileyin veya reklam engelleyiciyi kapatın.
                </p>
            </div>
        @endif

        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary px-5 py-2 btn-submit">
                <i class="fas fa-paper-plane me-2"></i> GÖNDER
            </button>
        </div>

    </form>
</div>

@once
@if($turnstileEnabled && $turnstileSiteKey)
    {{-- Implicit mode: async/defer OK; do NOT call turnstile.ready() --}}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endif
<script>
    async function submitContactForm(e) {
        e.preventDefault();

        const form = e.target;
        const container = form.closest('.contact-form-container');
        const msgBox = container.querySelector('.form-message');
        const btn = form.querySelector('.btn-submit');
        const originalBtnText = btn.innerHTML;
        const submitUrl = form.getAttribute('action');
        const turnstileOn = container.getAttribute('data-turnstile') === '1';

        if (turnstileOn) {
            const tokenInput = form.querySelector('[name="cf-turnstile-response"]');
            if (!tokenInput || !tokenInput.value) {
                msgBox.innerText = 'Lütfen güvenlik doğrulamasını tamamlayın.';
                msgBox.classList.add('alert-danger');
                msgBox.classList.remove('d-none', 'alert-success');
                return;
            }
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gönderiliyor...';

        msgBox.classList.add('d-none');
        msgBox.classList.remove('alert-success', 'alert-danger');

        const formData = new FormData(form);
        const jsonData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Platform': 'web'
                },
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();

            if (response.ok) {
                msgBox.innerText = 'Mesajınız başarıyla iletildi!';
                msgBox.classList.add('alert-success');
                msgBox.classList.remove('d-none');
                form.reset();
                if (window.turnstile) {
                    container.querySelectorAll('.cf-turnstile').forEach(function (el) {
                        try { window.turnstile.reset(el); } catch (err) {}
                    });
                }
            } else {
                throw new Error(result.message || 'Bir hata oluştu.');
            }
        } catch (error) {
            console.error('Form Hatası:', error);
            msgBox.innerText = error.message || 'Bağlantı hatası oluştu. Lütfen tekrar deneyiniz.';
            msgBox.classList.add('alert-danger');
            msgBox.classList.remove('d-none');
            if (window.turnstile) {
                container.querySelectorAll('.cf-turnstile').forEach(function (el) {
                    try { window.turnstile.reset(el); } catch (err) {}
                });
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        }
    }
</script>
@endonce
