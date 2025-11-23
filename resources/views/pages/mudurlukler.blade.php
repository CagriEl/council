@extends('layouts.master')

@section('title', 'Müdürlüklerimiz - T.C. Kırklareli Belediyesi')

@push('styles')
<style>
    /* SİZİN GÖNDERDİĞİNİZ CSS KODLARI */
    .vp-section { margin-bottom: 40px; }
    
    .vp-scroll-container {
        display: flex;
        gap: 15px;
        justify-content: center;
        overflow-x: auto;
        padding: 10px;
        -webkit-overflow-scrolling: touch;
    }

    .vp-card {
        min-width: 150px;
        width: 150px;
        background: white;
        border-radius: 12px;
        text-align: center;
        padding: 15px 5px;
        cursor: pointer;
        border: 2px solid transparent;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
    }

    .vp-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }

    .vp-card.active {
        border-color: #00c6ff;
        background: linear-gradient(to bottom, #ffffff, #f0f8ff);
        box-shadow: 0 10px 25px rgba(0, 198, 255, 0.2);
    }

    .vp-card.active::after {
        content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%);
        width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid #00c6ff;
    }

    .vp-img {
        width: 65px; height: 65px; border-radius: 50%; object-fit: cover;
        margin-bottom: 8px; border: 2px solid #eee;
    }
    .vp-card.active .vp-img { border-color: #00c6ff; }

    .vp-name { font-weight: 700; color: #1a3c6e; font-size: 0.85rem; margin-bottom: 2px; line-height: 1.2; }
    .vp-title { font-size: 0.65rem; color: #777; text-transform: uppercase; font-weight: 600; }

    /* ORGANİZASYON ŞEMASI */
    #orgChartView { display: none; }
    .org-group { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 30px; overflow: hidden; border: 1px solid #eee; }
    .org-header { background: linear-gradient(90deg, #1a3c6e, #2c3e50); padding: 15px 20px; display: flex; align-items: center; color: white; }
    .org-vp-img { width: 60px; height: 60px; border-radius: 50%; border: 3px solid white; object-fit: cover; margin-right: 15px; }
    .org-vp-info h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
    .org-vp-info span { font-size: 0.8rem; opacity: 0.9; font-weight: 300; text-transform: uppercase; }
    .org-list { padding: 0; margin: 0; list-style: none; }
    .org-item { padding: 12px 20px; border-bottom: 1px solid #f1f1f1; display: flex; align-items: center; justify-content: space-between; transition: background 0.2s; cursor: pointer; }
    .org-item:last-child { border-bottom: none; }
    .org-item:hover { background-color: #f9faff; }
    .org-dept-name { font-weight: 600; color: #444; display: flex; align-items: center; gap: 15px; font-size: 0.95rem; }
    .org-dept-icon { width: 35px; height: 35px; background: #eaf2ff; color: #1a3c6e; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    .org-arrow { color: #ccc; transition: transform 0.2s; }
    .org-item:hover .org-arrow { color: #1a3c6e; transform: translateX(5px); }

    /* MÜDÜR KARTI */
    .director-item { transition: all 0.4s ease; }
    .hidden-item { display: none; }
    .fade-in { animation: fadeIn 0.5s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .director-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); height: 100%; border: 1px solid #eee; display: flex; flex-direction: column; cursor: pointer; transition: transform 0.3s ease; }
    .director-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    .card-top-border { height: 5px; background: linear-gradient(90deg, #1a3c6e, #3498db); width: 100%; }
    .director-img-wrapper { height: 220px; overflow: hidden; position: relative; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; }
    .director-img { width: 100%; height: 100%; object-fit: cover; object-position: top center; }
    .dept-icon-badge { position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); width: 50px; height: 50px; background: #1a3c6e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; border: 4px solid white; z-index: 10; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .director-info { padding: 35px 20px 20px; text-align: center; flex-grow: 1; display: flex; flex-direction: column; align-items: center; }
    .director-dept { font-size: 0.85rem; font-weight: 800; color: #e74c3c; text-transform: uppercase; margin-bottom: 5px; }
    .director-name { font-weight: 700; font-size: 1.1rem; color: #1a3c6e; margin-bottom: 15px; }
    .btn-detail { margin-top: auto; width: 100%; background: #f8f9fa; color: #1a3c6e; border: 1px solid #eee; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
    .director-card:hover .btn-detail { background: #1a3c6e; color: white; }

    @media (max-width: 992px) {
        .detail-header-bg { border-radius: 0; }
        .detail-logo-container { justify-content: center; margin-bottom: 1rem; }
        .contact-info { justify-content: center; }
        .detail-nav { text-align: center; }
        .vp-scroll-container { justify-content: flex-start; padding-bottom: 15px; }
    }
</style>
@endpush

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
@endpush