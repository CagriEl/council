<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meclis Kararları - T.C. Kırklareli Belediyesi</title>
      @include('layouts.header')      
    
</head>
<body>

  

    <!-- İÇERİK -->
    <div class="container mb-5 mt-5">
        
        <!-- Başlık -->
        <div class="text-center mb-5">
            <h1 style="font-weight: 800; color: #1a3c6e;">Meclis Kararları ve Gündemi</h1>
            <div style="width: 80px; height: 4px; background: #e74c3c; margin: 15px auto; border-radius: 2px;"></div>
            <p class="text-muted">Meclis toplantı tutanaklarına, gündem maddelerine ve komisyon raporlarına aşağıdan ulaşabilirsiniz.</p>
        </div>

        <!-- YIL FİLTRESİ (TABS) -->
        <div class="year-tabs" id="yearTabs">
            <button class="year-tab-btn active" onclick="filterByYear('all', this)">TÜMÜ</button>
            @foreach($years as $year)
                <button class="year-tab-btn" onclick="filterByYear('{{ $year }}', this)">{{ $year }}</button>
            @endforeach
        </div>

        <!-- ACCORDION LİSTESİ -->
        <div class="accordion" id="decisionsAccordion">
            
            @forelse($decisions as $index => $decision)
                {{-- Veritabanında 'year' sütunu yoksa tarihten yıl türetilir, varsa direkt kullanılır --}}
                @php
                    $decisionYear = $decision->year ?? (\Carbon\Carbon::parse($decision->date)->year ?? date('Y'));
                @endphp

                <div class="accordion-item year-item" data-year="{{ $decisionYear }}">
                    
                    <!-- Başlık Kısmı (Tıklanabilir) -->
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <div class="badge bg-primary rounded-pill px-3 py-2">{{ $decisionYear }}</div>
                                <div>{{ $decision->title }}</div>
                            </div>
                        </button>
                    </h2>

                    <!-- Açılır İçerik -->
                    <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#decisionsAccordion">
                        <div class="accordion-body">
                            <div class="decision-files-row">
                                
                                <!-- 1. MECLİS GÜNDEMİ -->
                                @if($decision->agenda_file)
                                    <a href="{{ Storage::url($decision->agenda_file) }}" target="_blank" class="file-card file-gundem">
                                        <i class="fas fa-file-alt file-icon"></i>
                                        <span class="file-title">MECLİS GÜNDEMİ</span>
                                        <span class="small text-muted mt-1">Görüntülemek İçin Tıklayın</span>
                                    </a>
                                @else
                                    <div class="file-card disabled-file">
                                        <i class="fas fa-file-excel file-icon text-muted"></i>
                                        <span class="file-title text-muted">GÜNDEM YOK</span>
                                    </div>
                                @endif

                                <!-- 2. MECLİS KARARLARI -->
                                @if($decision->decision_file)
                                    <a href="{{ Storage::url($decision->decision_file) }}" target="_blank" class="file-card file-karar">
                                        <i class="fas fa-gavel file-icon"></i>
                                        <span class="file-title">MECLİS KARARLARI</span>
                                        <span class="small text-muted mt-1">Görüntülemek İçin Tıklayın</span>
                                    </a>
                                @else
                                    <div class="file-card disabled-file">
                                        <i class="fas fa-file-excel file-icon text-muted"></i>
                                        <span class="file-title text-muted">KARAR YOK</span>
                                    </div>
                                @endif

                                <!-- 3. KOMİSYON RAPORLARI -->
                                @if($decision->commission_file)
                                    <a href="{{ Storage::url($decision->commission_file) }}" target="_blank" class="file-card file-komisyon">
                                        <i class="fas fa-users-cog file-icon"></i>
                                        <span class="file-title">KOMİSYON RAPORLARI</span>
                                        <span class="small text-muted mt-1">Görüntülemek İçin Tıklayın</span>
                                    </a>
                                @else
                                    <div class="file-card disabled-file">
                                        <i class="fas fa-file-excel file-icon text-muted"></i>
                                        <span class="file-title text-muted">RAPOR YOK</span>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded shadow-sm border">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Henüz meclis kararı bulunmamaktadır.</h4>
                    </div>
                </div>
            @endforelse

        </div>
        
        <!-- Yıl Filtrelemesi Sonucu Boşsa -->
        <div id="noDataMessage" class="text-center py-5" style="display: none;">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Seçilen yıla ait kayıt bulunamadı.</h4>
        </div>

    </div>

    <!-- Scriptler -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterByYear(year, btn) {
            // 1. Buton Aktifliği
            document.querySelectorAll('.year-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // 2. Filtreleme Mantığı
            const items = document.querySelectorAll('.year-item');
            let hasVisible = false;

            items.forEach(item => {
                if (year === 'all' || item.getAttribute('data-year') === year) {
                    item.style.display = 'block';
                    hasVisible = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // 3. Boş Mesajı
            const noDataMsg = document.getElementById('noDataMessage');
            if (!hasVisible) {
                noDataMsg.style.display = 'block';
            } else {
                noDataMsg.style.display = 'none';
            }
        }
    </script>
</body>
</html>