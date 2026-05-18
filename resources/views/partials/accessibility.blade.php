{{-- Erişilebilirlik: atlama bağlantısı, araç çubuğu, görsel yardımcı stiller --}}
<a href="#main-content" class="skip-to-content">İçeriğe geç</a>

<div class="a11y-toolbar" data-a11y-toolbar>
    <button type="button" class="a11y-toolbar-toggle" data-a11y-toggle aria-expanded="false" aria-controls="a11y-panel" aria-label="Erişilebilirlik ayarlarını aç veya kapat">
        <i class="fas fa-universal-access" aria-hidden="true"></i>
        <span class="a11y-toolbar-label">Erişilebilirlik</span>
    </button>
    <div id="a11y-panel" class="a11y-panel" data-a11y-panel hidden role="dialog" aria-label="Erişilebilirlik seçenekleri">
        <p class="a11y-panel-intro">Görüntüleme tercihlerinizi aşağıdan ayarlayabilirsiniz. Seçimleriniz bu cihazda saklanır.</p>
        <div class="a11y-panel-actions">
            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" data-a11y-font-dec>Metni küçült</button>
            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" data-a11y-font-inc>Metni büyüt</button>
            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" data-a11y-hc>Yüksek kontrast</button>
            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" data-a11y-ul>Bağlantıları altı çizili göster</button>
            <button type="button" class="btn btn-sm btn-primary w-100" data-a11y-reset>Tümünü sıfırla</button>
        </div>
    </div>
</div>

<style>
    .skip-to-content {
        position: absolute;
        left: -9999px;
        top: 12px;
        z-index: 100001;
        padding: 12px 20px;
        background: #000;
        color: #fff !important;
        font-weight: 700;
        text-decoration: none;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .skip-to-content:focus {
        left: 12px;
        outline: 3px solid #fc0;
        outline-offset: 2px;
    }
    main#main-content:focus { outline: none; }
    .outline-none:focus { outline: none; }

    .a11y-toolbar {
        position: fixed;
        right: 16px;
        bottom: 16px;
        z-index: 1040;
        font-family: 'Poppins', system-ui, sans-serif;
    }
    .a11y-toolbar-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        border: none;
        border-radius: 50px;
        background: linear-gradient(90deg, #0052cc, #00c6ff);
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(0, 82, 204, 0.45);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .a11y-toolbar-toggle:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0, 82, 204, 0.55); }
    .a11y-toolbar-toggle:focus { outline: 3px solid #fc0; outline-offset: 2px; }
    .a11y-toolbar-toggle i { font-size: 1.25rem; }
    @media (max-width: 576px) {
        .a11y-toolbar-label { display: none; }
        .a11y-toolbar-toggle { padding: 14px; border-radius: 50%; }
    }
    .a11y-panel {
        position: absolute;
        right: 0;
        bottom: calc(100% + 10px);
        width: min(320px, calc(100vw - 32px));
        padding: 16px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border: 1px solid #e9ecef;
    }
    .a11y-panel[hidden] { display: none !important; }
    .a11y-panel-intro { font-size: 0.85rem; color: #555; margin-bottom: 12px; line-height: 1.45; }

    html.a11y-fs-1 { font-size: 106.25%; }
    html.a11y-fs-2 { font-size: 112.5%; }
    html.a11y-fs-3 { font-size: 118.75%; }

    html.a11y-hc body {
        background: #fff !important;
        color: #000 !important;
    }
    html.a11y-hc a { color: #0000cd !important; }
    html.a11y-hc a:visited { color: #551a8b !important; }
    html.a11y-hc .header-wrapper,
    html.a11y-hc .header-solid,
    html.a11y-hc .detail-header-bg,
    html.a11y-hc .internal-header,
    html.a11y-hc .hero-overlay {
        background: #0d47a1 !important;
        background-image: none !important;
    }
    html.a11y-hc .header-topbar {
        background: #082f5c !important;
        border-bottom-color: #fff !important;
    }
    html.a11y-hc .header-topbar-weather,
    html.a11y-hc .header-topbar-contact { color: #fff !important; }
    html.a11y-hc .header-topbar-weather:hover { background: #fff !important; color: #000 !important; }
    html.a11y-hc .custom-nav .nav-link,
    html.a11y-hc .detail-nav .nav-link { color: #fff !important; text-shadow: none !important; }
    html.a11y-hc .btn-primary { background: #000 !important; border-color: #000 !important; color: #fff !important; }

    html.a11y-ul a:not(.skip-to-content) { text-decoration: underline !important; }
</style>

<script>
(function () {
    var root = document.documentElement;
    var KEY = 'kirklareli_a11y_v1';

    function read() {
        try { return JSON.parse(localStorage.getItem(KEY) || '{}'); } catch (e) { return {}; }
    }
    function write(data) {
        localStorage.setItem(KEY, JSON.stringify(data));
        apply(data);
    }
    function apply(data) {
        data = data || read();
        var fs = Math.min(3, Math.max(0, parseInt(data.fs, 10) || 0));
        root.classList.remove('a11y-fs-1', 'a11y-fs-2', 'a11y-fs-3');
        if (fs > 0) root.classList.add('a11y-fs-' + fs);
        root.classList.toggle('a11y-hc', !!data.hc);
        root.classList.toggle('a11y-ul', !!data.ul);
    }

    document.addEventListener('DOMContentLoaded', function () {
        apply();

        var toolbar = document.querySelector('[data-a11y-toolbar]');
        if (!toolbar) return;
        var toggle = toolbar.querySelector('[data-a11y-toggle]');
        var panel = toolbar.querySelector('[data-a11y-panel]');

        toggle.addEventListener('click', function () {
            var open = panel.hasAttribute('hidden');
            if (open) {
                panel.removeAttribute('hidden');
                toggle.setAttribute('aria-expanded', 'true');
            } else {
                panel.setAttribute('hidden', 'hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        toolbar.querySelector('[data-a11y-font-inc]').addEventListener('click', function () {
            var d = read();
            d.fs = Math.min(3, (parseInt(d.fs, 10) || 0) + 1);
            write(d);
        });
        toolbar.querySelector('[data-a11y-font-dec]').addEventListener('click', function () {
            var d = read();
            d.fs = Math.max(0, (parseInt(d.fs, 10) || 0) - 1);
            write(d);
        });
        toolbar.querySelector('[data-a11y-hc]').addEventListener('click', function () {
            var d = read();
            d.hc = !d.hc;
            write(d);
        });
        toolbar.querySelector('[data-a11y-ul]').addEventListener('click', function () {
            var d = read();
            d.ul = !d.ul;
            write(d);
        });
        toolbar.querySelector('[data-a11y-reset]').addEventListener('click', function () {
            write({ fs: 0, hc: false, ul: false });
            panel.setAttribute('hidden', 'hidden');
            toggle.setAttribute('aria-expanded', 'false');
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && panel && !panel.hasAttribute('hidden')) {
                panel.setAttribute('hidden', 'hidden');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
            }
        });
    });
})();
</script>
