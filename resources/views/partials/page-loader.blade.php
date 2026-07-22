{{-- Sayfa yüklenirken belediye logosu; ham CSS/JS flash'ını gizler --}}
@once
<style>
    html.kb-loading,
    html.kb-loading body {
        overflow: hidden !important;
    }
    #kb-page-loader {
        position: fixed;
        inset: 0;
        z-index: 2147483000;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.25rem;
        background: linear-gradient(160deg, #003d99 0%, #0052cc 45%, #00a0e0 100%);
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }
    #kb-page-loader.is-done {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    #kb-page-loader .kb-loader-logo {
        width: min(160px, 42vw);
        height: auto;
        filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.35));
        animation: kb-loader-pulse 1.5s ease-in-out infinite;
    }
    #kb-page-loader .kb-loader-title {
        margin: 0;
        color: #fff;
        font-family: 'Poppins', system-ui, sans-serif;
        font-size: clamp(0.95rem, 2.5vw, 1.15rem);
        font-weight: 700;
        letter-spacing: 0.04em;
        text-align: center;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }
    #kb-page-loader .kb-loader-bar {
        width: 120px;
        height: 3px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.25);
        overflow: hidden;
    }
    #kb-page-loader .kb-loader-bar > span {
        display: block;
        height: 100%;
        width: 40%;
        border-radius: inherit;
        background: #fff;
        animation: kb-loader-slide 1.1s ease-in-out infinite;
    }
    @keyframes kb-loader-pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.04); opacity: 0.92; }
    }
    @keyframes kb-loader-slide {
        0% { transform: translateX(-120%); }
        100% { transform: translateX(320%); }
    }
</style>

<div id="kb-page-loader" role="status" aria-live="polite" aria-label="Sayfa yükleniyor">
    <img
        class="kb-loader-logo"
        src="{{ asset('images/logo.png') }}"
        alt="T.C. Kırklareli Belediyesi"
        width="160"
        height="160"
        decoding="async"
        fetchpriority="high"
    >
    <p class="kb-loader-title">T.C. Kırklareli Belediyesi</p>
    <div class="kb-loader-bar" aria-hidden="true"><span></span></div>
</div>

<script>
(function () {
    if (window.__kbLoaderBound) return;
    window.__kbLoaderBound = true;
    document.documentElement.classList.add('kb-loading');

    function hideLoader() {
        var el = document.getElementById('kb-page-loader');
        document.documentElement.classList.remove('kb-loading');
        if (!el || el.classList.contains('is-done')) return;
        el.classList.add('is-done');
        window.setTimeout(function () {
            if (el && el.parentNode) el.parentNode.removeChild(el);
        }, 450);
    }

    if (document.readyState === 'complete') {
        hideLoader();
    } else {
        window.addEventListener('load', hideLoader);
        window.setTimeout(hideLoader, 6000);
    }
})();
</script>
@endonce
