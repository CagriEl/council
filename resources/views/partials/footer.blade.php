<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <img src="{{ asset('storage/' . ($settings['logo'] ?? 'logo.png')) }}" alt="Logo" class="footer-logo">
                <p class="footer-desc">
                    {{ $settings['footer_text'] ?? 'Kırklareli Belediyesi, şeffaf...' }}
                </p>
                <div class="social-links">
                    @if(isset($settings['facebook']))
                        <a href="{{ $settings['facebook'] }}" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    <!-- Diğer sosyal medya ikonları -->
                </div>
            </div>
            <!-- Diğer Footer Sütunları -->
        </div>
    </div>
    <div class="copyright">
        <div class="container">
             &copy; {{ date('Y') }} T.C. Kırklareli Belediyesi. Tüm Hakları Saklıdır.
        </div>
    </div>
</footer>