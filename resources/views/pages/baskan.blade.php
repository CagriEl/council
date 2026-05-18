<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belediye Başkanı - {{ $mayor->name ?? 'T.C. Kırklareli Belediyesi' }}</title>
      @include('layouts.frontend-head')    
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container mb-5">
        <div class="mayor-tabs-wrapper">
            <ul class="nav nav-pills mb-4 justify-content-center justify-content-lg-start" id="mayorTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="bio-tab" data-bs-toggle="pill" data-bs-target="#bio" type="button" role="tab">
                        <i class="fas fa-user-tie me-2"></i> BAŞKANIN ÖZGEÇMİŞİ
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="message-tab" data-bs-toggle="pill" data-bs-target="#message" type="button" role="tab">
                        <i class="fas fa-envelope-open-text me-2"></i> BAŞKANIN MESAJI
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ask-tab" data-bs-toggle="pill" data-bs-target="#ask" type="button" role="tab">
                        <i class="fas fa-question-circle me-2"></i> BAŞKANA SOR
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="mayorTabsContent">
                <!-- ÖZGEÇMİŞ TABI (DİNAMİK) -->
                <div class="tab-pane fade show active" id="bio" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8 order-2 order-lg-1">
                            <!-- İsim Veritabanından -->
                            <h2 class="section-title">{{ $mayor->name ?? 'Belediye Başkanı' }} Kimdir?</h2>
                            <div class="text-content">
                                <!-- Modelde 'biography' yok, 'description' var -->
                                {!! $mayor->description ?? '<p>Biyografi bilgisi henüz eklenmedi.</p>' !!}
                            </div>
                        </div>
                        <div class="col-lg-4 order-1 order-lg-2 text-center">
                            <!-- Modelde 'image_path' var. asset() ile dosya yolunu oluşturuyoruz. -->
                            <!-- Eğer image_path null ise placeholder gösterebilirsiniz -->
                            <img src="{{ $mayor->image_path ? asset('storage/' . $mayor->image_path) : 'https://via.placeholder.com/600x800' }}" alt="{{ $mayor->name }}" class="mayor-bio-img">
                            
                            <!-- İsim ve Unvan Veritabanından -->
                            <div class="mt-3 fw-bold fs-5 text-center" style="color:#1a3c6e;">{{ $mayor->name }}</div>
                            <div class="text-muted text-center small">{{ $mayor->title ?? 'Belediye Başkanı' }}</div>
                        </div>
                    </div>
                </div>

                <!-- MESAJ TABI (DİNAMİK + GÖRSELLİ) -->
                <div class="tab-pane fade" id="message" role="tabpanel">
                    <div class="row">
                        <!-- Metin Alanı (Sol) -->
                        <div class="col-lg-8 order-2 order-lg-1">
                            <h2 class="section-title">Başkanın Mesajı</h2>
                            
                            <div class="message-box mb-4">
                                {{-- Modelde 'quote' alanı olmadığı için statik, ancak dinamik mesaj aşağıda --}}
                                {!! $mayor->message ?? '<p>Henüz bir mesaj eklenmedi.</p>' !!}
                            </div>
                            <div class="mt-4 text-end">
                                <div class="fw-bold mt-2 text-primary">{{ $mayor->name }}</div>
                                <div class="small text-muted">{{ $mayor->title ?? 'Belediye Başkanı' }}</div>
                            </div>
                        </div>

                        <!-- Görsel Alanı (Sağ) - Aynı görseli kullanıyoruz -->
                        <div class="col-lg-4 order-1 order-lg-2 text-center">
                            <img src="{{ $mayor->image_path ? asset('storage/' . $mayor->image_path) : 'https://via.placeholder.com/600x800' }}" alt="{{ $mayor->name }}" class="mayor-bio-img">
                        </div>
                    </div>
                </div>

                <!-- SORU SOR TABI -->
                <div class="tab-pane fade" id="ask" role="tabpanel">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h2 class="section-title text-center w-100">Başkana Sor</h2>
                            <p class="text-center mb-5 text-muted">Öneri, talep ve şikayetlerinizi doğrudan başkana iletebilirsiniz.</p>
                            <x-contact-form source="baskan-sayfasi" />
                            {{-- Form Action'ı projenizdeki uygun route'a yönlendirin --}}
                            {{-- <form action="{{ route('contact.submit') }}" method="POST"> --}}
                                @csrf
                            
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>