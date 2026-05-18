@props(['source' => 'genel', 'title' => null])

<div class="contact-form-container">
    @if($title)
        <h3 class="mb-4 fw-bold text-dark border-bottom pb-2">{{ $title }}</h3>
    @endif

    <!-- Bildirim Alanı (Başlangıçta Gizli) -->
    <div class="form-message alert d-none" role="alert"></div>

    <form action="{{ url('api/contact/submit') }}" onsubmit="submitContactForm(event)" class="row g-3 p-4 border rounded shadow-sm bg-white">
        
        <input type="hidden" name="source" value="{{ $source }}">
        <input type="hidden" name="platform" value="web">

        <div class="col-md-6">
            <label class="form-label fw-bold">Adınız Soyadınız</label>
            <input type="text" name="name" class="form-control" placeholder="Adınız Soyadınız" required>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Telefon Numaranız</label>
            <input type="tel" name="phone" class="form-control" placeholder="05XX XXX XX XX">
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">E-Posta Adresiniz</label>
            <input type="email" name="email" class="form-control" placeholder="ornek@mail.com">
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
            <textarea name="message" class="form-control" rows="5" placeholder="Mesajınızı buraya yazınız..." required></textarea>
        </div>

        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary px-5 py-2 btn-submit">
                <i class="fas fa-paper-plane me-2"></i> GÖNDER
            </button>
        </div>

    </form>
</div>

@once
<script>
    async function submitContactForm(e) {
        e.preventDefault();
        
        const form = e.target;
        // Formun bulunduğu kapsayıcıyı buluyoruz
        const container = form.closest('.contact-form-container');
        // Mesaj kutusunu ve butonu kapsayıcı içinden seçiyoruz (ID kullanmadan)
        const msgBox = container.querySelector('.form-message');
        const btn = form.querySelector('.btn-submit');
        const originalBtnText = btn.innerHTML;
        const submitUrl = form.getAttribute('action');

        // UI Güncelleme: Yükleniyor
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gönderiliyor...';
        
        // Mesaj kutusunu gizle ve temizle
        msgBox.classList.add('d-none');
        msgBox.classList.remove('alert-success', 'alert-danger');

        // Veriyi Hazırla
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
                // Başarılı
                msgBox.innerText = 'Mesajınız başarıyla iletildi!';
                msgBox.classList.add('alert-success'); // Bootstrap yeşil alert
                msgBox.classList.remove('d-none');
                form.reset();
            } else {
                // Sunucu Hatası
                throw new Error(result.message || 'Bir hata oluştu.');
            }
        } catch (error) {
            // Bağlantı Hatası
            console.error('Form Hatası:', error);
            msgBox.innerText = error.message || 'Bağlantı hatası oluştu. Lütfen tekrar deneyiniz.';
            msgBox.classList.add('alert-danger'); // Bootstrap kırmızı alert
            msgBox.classList.remove('d-none');
        } finally {
            // Butonu eski haline getir
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        }
    }
</script>
@endonce