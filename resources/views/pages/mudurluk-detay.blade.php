@extends('layouts.header')

{{-- HATA BURADAYDI: $directorate yerine $mudurluk yaptık --}}
@section('title', $mudurluk->name . ' - T.C. Kırklareli Belediyesi')

@section('content')

<div class="container mb-5">
    <div class="row">
        
        <!-- SOL: Müdür Bilgisi -->
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="dept-sidebar-card">
                <!-- Müdür Görseli -->
                @if($mudurluk->manager_image)
                    <img src="{{ Storage::url($mudurluk->manager_image) }}" alt="{{ $mudurluk->manager_name }}" class="dept-manager-img">
                @else
                    <!-- Fotoğraf yoksa baş harflerden avatar oluştur -->
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mudurluk->manager_name ?? $mudurluk->name) }}&background=0052cc&color=fff&size=140" alt="{{ $mudurluk->manager_name }}" class="dept-manager-img">
                @endif

                <div class="dept-manager-name">{{ $mudurluk->manager_name }}</div>
                <div class="dept-manager-title">{{ $mudurluk->displayManagerRole() }}</div>
                <hr>
                
                <ul class="contact-list">
                    @if($mudurluk->phone)
                        <li><i class="fas fa-phone"></i> {{ $mudurluk->phone }}</li>
                    @else
                        <li><i class="fas fa-phone"></i> 444 01 39</li>
                    @endif

                    @if($mudurluk->email)
                        <li><i class="fas fa-envelope"></i> {{ $mudurluk->email }}</li>
                    @endif

                    <li><i class="fas fa-map-marker-alt"></i> Belediye Ana Hizmet Binası</li>
                </ul>
            </div>
        </div>

        <!-- SAĞ: Müdürlük Hakkında -->
        <div class="col-lg-8">
            <div class="content-card">
                <!-- Başlık -->
                <h1 class="page-title">
                    <i class="fas fa-building"></i>
                    {{ $mudurluk->name }}
                </h1>
                
                <!-- İçerik (Panelden gelen HTML) -->
                <div class="content-text">
                    @if($mudurluk->description)
                        {!! \App\Support\HtmlContentSanitizer::stripKaynakSayfayiAcBlocks((string) ($mudurluk->description ?? '')) !!}
                    @else
                        <p>Bu müdürlük için henüz detaylı bilgi girilmemiştir.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@include('layouts.footer')
@endsection


