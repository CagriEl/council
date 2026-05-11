<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }} - T.C. Kırklareli Belediyesi</title>
   @include('layouts.frontend-head')

</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container mb-5">
        <div class="row">
            <!-- SOL SIDEBAR: DİĞER HABERLER -->
            <div class="col-lg-3 mb-4 mb-lg-0">
                <h3 class="sidebar-title">DİĞER HABERLER</h3>
                @php
                    $otherNews = \App\Models\News::publishedForPublic()->where('id', '!=', $news->id)->latest('published_at')->take(5)->get();
                @endphp
                @foreach($otherNews as $item)
                    <a href="{{ route('news.detail', $item->slug) }}" class="news-list-item">
                        <span class="badge-cat">HABER</span>
                        <div style="font-weight:700; font-size:0.9rem;">{{ $item->title }}</div>
                        @php
                            $newsSide = \Illuminate\Support\Carbon::parse($item->published_at ?? $item->created_at)->locale('tr');
                        @endphp
                        <div style="font-size:0.75rem; color:#999;">{{ $newsSide->isoFormat('DD.MM.YYYY') }}</div>
                    </a>
                @endforeach
            </div>

            <!-- SAĞ TARAF: HABER DETAYI -->
            <div class="col-lg-9 ps-lg-5">
                @php
                    $newsMainDate = \Illuminate\Support\Carbon::parse($news->published_at ?? $news->created_at)->locale('tr');
                @endphp
                <div class="content-date"><i class="far fa-calendar-alt me-2"></i>{{ $newsMainDate->isoFormat('DD.MM.YYYY dddd') }}</div>
                <h1 class="content-title">{{ $news->title }}</h1>
                
                <div class="row">
                    <div class="col-12">
                        @if($news->image_path)
                            <img src="{{ asset('storage/' . $news->image_path) }}" alt="{{ $news->title }}" class="featured-image float-lg-end ms-lg-4 mb-3" style="max-width: 50%; height: auto;">
                        @endif
                        <div class="content-text">{!! \App\Support\HtmlContentSanitizer::stripKaynakSayfayiAcBlocks((string) $news->content) !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')

</html>