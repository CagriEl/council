<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Frontend\AnnouncementController;
use App\Http\Controllers\Frontend\NewsController;
use App\Models\CouncilMember;
use App\Models\CouncilDecision;
use App\Models\Mayor;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ana Sayfa
Route::get('/', [PageController::class, 'home'])->name('home');

// Mevcut Route::view('/baskan', ...) satırını silip yerine şunu yapıştırın:
Route::get('/baskan', function () {
    // Veritabanından aktif başkanı çek, yoksa boş bir nesne oluştur
    // (Böylece sayfa hata vermeden açılır)
    $mayor = Mayor::where('is_active', true)->first() ?? new Mayor();
    
    return view('pages.baskan', compact('mayor'));
})->name('baskan');

Route::get('/meclis', [PageController::class, 'meclis'])->name('meclis');

// HATA ALDIĞINIZ KISIM BURASIYDI:
// Metod ismi 'mudurlukler' değil, 'mudurler' olmalı.
Route::get('/mudurler', [PageController::class, 'mudurler'])->name('mudurler');

// Müdürlük Detay Sayfası
Route::get('/mudurluk/{slug}', [PageController::class, 'mudurlukDetay'])->name('mudurluk.detay');


Route::get('/duyurular/{slug}', [AnnouncementController::class, 'show'])->name('announcement.show');

Route::get('/haber/{slug}', [NewsController::class, 'show'])->name('news.detail');

Route::get('/sayfa/{slug}', [App\Http\Controllers\Frontend\PageController::class, 'show'])->name('page.detail');

Route::get('/meclis-uyeleri', function () {
    // Veritabanından aktif üyeleri çekiyoruz
    // Eğer sort_order sütunu yoksa hata almamak için veritabanı yapınızı kontrol edin
    $members = CouncilMember::where('is_active', true)
        ->orderBy('sort_order', 'asc') 
        ->orderBy('name', 'asc')
        ->get();

    return view('pages.meclis', compact('members'));
})->name('council.index');


Route::get('/meclis-kararlari', function () {
    // 1. Filtreleme için son 5 yılı oluştur
    $currentYear = date('Y');
    $years = range($currentYear, $currentYear - 5);

    // 2. Veritabanından Kararları Çek
    // DÜZELTME: 'date' yerine sizin tablonuzdaki 'meeting_date' sütununa göre sıralıyoruz.
    $decisions = CouncilDecision::orderBy('meeting_date', 'desc')->get();

    return view('pages.meclis-kararlari', compact('years', 'decisions'));
})->name('meclis-kararlari');

Route::get('/iletisim', function () {
    return view('pages.iletisim');
})->name('iletisim');