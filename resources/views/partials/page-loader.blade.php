{{-- Sayfa yüklenirken belediye logosu; ham CSS/JS flash'ını gizler --}}
@once
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
            try { window.dispatchEvent(new Event('kb:ready')); } catch (e) {}
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
