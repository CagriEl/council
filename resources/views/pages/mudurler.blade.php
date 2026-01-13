@extends('layouts.header')

@section('title', 'Müdürlüklerimiz - T.C. Kırklareli Belediyesi')



@section('content')

    <!-- İÇERİK -->
    <div class="container mb-5">
        <div class="page-title-wrapper text-center mb-5">
            <h1 class="page-title" style="font-weight: 800; font-size: 2.2rem; color: #1a3c6e; margin-bottom: 10px;">İdari Organizasyon Şeması</h1>
            <div class="title-divider" style="width: 80px; height: 4px; background: #e74c3c; margin: 15px auto; border-radius: 2px;"></div>
            <p class="page-subtitle" style="color: #777; font-size: 1.1rem;">Müdürlükleri filtrelemek veya organizasyon şemasını görüntülemek için aşağıdaki seçenekleri kullanabilirsiniz.</p>
        </div>

        <!-- 1. FİLTRELEME VE ŞEMA BUTONLARI -->
        <div class="vp-section">
            <div class="vp-scroll-container">
                
                <!-- Organizasyon Şeması Butonu -->
                <div class="vp-card" onclick="showOrgChart(this)" style="border-color: #e74c3c; background: #fff5f5;">
                    <div style="font-size: 2rem; margin-bottom: 5px; color: #e74c3c;">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="vp-name" style="color: #e74c3c; font-size: 0.8rem;">ORGANİZASYON<br>ŞEMASI</div>
                    <div class="vp-title" style="font-size: 0.65rem;">Tüm Liste</div>
                </div>

                <!-- Tümü Butonu -->
                <div class="vp-card active" onclick="filterDirectorates('all', this)">
                    <img src="https://cdn-icons-png.flaticon.com/512/942/942748.png" alt="Tümü" class="vp-img">
                    <div class="vp-name">TÜM MÜDÜRLÜKLER</div>
                    <div class="vp-title">Genel Liste</div>
                </div>

                <!-- Dinamik Başkan Yardımcıları -->
                @foreach($vicePresidents as $vp)
                <div class="vp-card" onclick="filterDirectorates('vp-{{ $vp->id }}', this)">
                    @if($vp->image_path)
                        <img src="{{ Storage::url($vp->image_path) }}" alt="{{ $vp->name }}" class="vp-img">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($vp->name) }}&background=random" alt="{{ $vp->name }}" class="vp-img">
                    @endif
                    <div class="vp-name">{{ $vp->name }}</div>
                    <div class="vp-title">Başkan Yardımcısı</div>
                </div>
                @endforeach

            </div>
        </div>

        <!-- 2. MÜDÜRLÜKLER GRİD (Filtrelenecek Alan) -->
        <div class="row g-4 justify-content-center" id="directoratesGrid">

            @foreach($allDirectorates as $directorate)
            <!-- 
                 data-vp özelliği: Eğer müdürlük bir başkana bağlıysa 'vp-ID', değilse 'vp-none' 
                 Böylece JS ile filtreleme yapabiliyoruz.
            -->
            <div class="col-12 col-md-6 col-lg-3 director-item" data-vp="vp-{{ $directorate->vice_president_id ?? 'none' }}">
                <div class="director-card" onclick="window.location.href='{{ route('mudurluk.detay', $directorate->slug) }}'">
                    <div class="card-top-border"></div>
                    <div class="director-img-wrapper">
                        @if($directorate->manager_image)
                            <img src="{{ Storage::url($directorate->manager_image) }}" class="director-img" alt="Müdür">
                        @else
                             <!-- Placeholder -->
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($directorate->manager_name ?? $directorate->name) }}&background=1a3c6e&color=fff&size=200" class="director-img" alt="Müdür">
                        @endif
                        <div class="dept-icon-badge"><i class="fas fa-building"></i></div>
                    </div>
                    <div class="director-info">
                        <div class="director-dept">{{ $directorate->name }}</div>
                        <div class="director-name">{{ $directorate->manager_name ?? 'Müdür V.' }}</div>
                        <button class="btn-detail">Detayları Gör <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <!-- 3. ORGANİZASYON ŞEMASI LİSTESİ (Gizli) -->
        <div id="orgChartView" class="fade-in">
            
            @foreach($vicePresidents as $vp)
            <div class="org-group">
                <div class="org-header">
                    @if($vp->image_path)
                        <img src="{{ Storage::url($vp->image_path) }}" class="org-vp-img">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($vp->name) }}&background=fff&color=000" class="org-vp-img">
                    @endif
                    <div class="org-vp-info">
                        <h3>{{ $vp->name }}</h3>
                        <span>BAŞKAN YARDIMCISI</span>
                    </div>
                </div>
                <ul class="org-list">
                    @foreach($vp->directorates as $dir)
                    <li class="org-item" onclick="window.location.href='{{ route('mudurluk.detay', $dir->slug) }}'">
                        <div class="org-dept-name">
                            <div class="org-dept-icon"><i class="fas fa-folder"></i></div> 
                            {{ $dir->name }}
                        </div>
                        <i class="fas fa-chevron-right org-arrow"></i>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach

            <!-- Başkan Yardımcısına Bağlı Olmayanlar (Varsa) -->
            @php
                $independentDirectorates = $allDirectorates->whereNull('vice_president_id');
            @endphp

            @if($independentDirectorates->count() > 0)
            <div class="org-group">
                <div class="org-header" style="background: #2c3e50;">
                    <div class="org-vp-info" style="padding-left: 10px;">
                        <h3>Diğer Birimler</h3>
                        <span>BAŞKANLIK MAKAMINA BAĞLI / DİĞER</span>
                    </div>
                </div>
                <ul class="org-list">
                    @foreach($independentDirectorates as $dir)
                    <li class="org-item" onclick="window.location.href='{{ route('mudurluk.detay', $dir->slug) }}'">
                        <div class="org-dept-name">
                            <div class="org-dept-icon"><i class="fas fa-folder"></i></div> 
                            {{ $dir->name }}
                        </div>
                        <i class="fas fa-chevron-right org-arrow"></i>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>

    </div>

@endsection

@push('scripts')
<script>
    const gridView = document.getElementById('directoratesGrid');
    const orgView = document.getElementById('orgChartView');

    // FİLTRELEME FONKSİYONU
    function filterDirectorates(vpId, element) {
        // Görünüm Modunu Ayarla (Grid aktif)
        gridView.style.display = 'flex';
        orgView.style.display = 'none';

        // Aktif Sınıfını Değiştir
        document.querySelectorAll('.vp-card').forEach(card => {
            card.classList.remove('active');
            // Org butonu stilini sıfırla
            if(card.onclick.toString().includes('showOrgChart')) {
                card.style.borderColor = '#e74c3c';
                card.style.backgroundColor = '#fff5f5';
                card.querySelector('div').style.color = '#e74c3c';
                card.querySelector('.vp-name').style.color = '#e74c3c';
            }
        });
        element.classList.add('active');

        // Kartları Filtrele
        const items = document.querySelectorAll('.director-item');
        
        items.forEach(item => {
            // Önce gizle
            item.classList.add('hidden-item');
            item.classList.remove('fade-in');

            // Eğer 'all' seçiliyse hepsini göster
            // Eğer belirli bir VP seçiliyse ID eşleşenleri göster
            if (vpId === 'all' || item.getAttribute('data-vp') === vpId) {
                setTimeout(() => {
                    item.classList.remove('hidden-item');
                    item.classList.add('fade-in');
                }, 50);
            }
        });
    }

    // ŞEMA GÖRÜNÜMÜNÜ GÖSTER
    function showOrgChart(element) {
        // Grid'i gizle, Şemayı göster
        gridView.style.display = 'none';
        orgView.style.display = 'block';

        // Diğer butonların aktifliğini kaldır
        document.querySelectorAll('.vp-card').forEach(card => {
            card.classList.remove('active');
        });

        // Bu butonu aktif yap
        element.classList.add('active');
        element.style.backgroundColor = '#e74c3c';
        element.style.borderColor = '#e74c3c';
        element.querySelector('div').style.color = 'white';
        element.querySelector('.vp-name').style.color = 'white';
    }
</script>
@include('layouts.footer')

@endpush
