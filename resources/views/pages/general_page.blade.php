<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - T.C. Kırklareli Belediyesi</title>
   @include('layouts.header')    
</head>
<body>



    <!-- İÇERİK (Sidebarsız - Merkez) -->
    <div class="container mb-5">
        
        <div class="page-container">
            <!-- Başlık -->
            <h1 class="page-title">{{ $page->title }}</h1>

            <!-- Kapak Görseli (Varsa) -->
            @if($page->image_path)
                <img src="{{ asset('storage/' . $page->image_path) }}" alt="{{ $page->title }}" class="page-featured-image">
            @endif

            <!-- İçerik -->
            <div class="page-content">
                {!! $page->content !!}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

@include('layouts.footer')
</html>