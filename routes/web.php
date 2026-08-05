<?php

use App\Http\Controllers\Frontend\AnnouncementController;
use App\Http\Controllers\Frontend\NewsController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\TransparencyController;
use App\Http\Controllers\PageController;
use App\Models\ActivityReport;
use App\Models\CouncilDecision;
use App\Models\Mayor;
use App\Models\StrategicPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// API dokümantasyonu (Swagger UI) — tarayıcı: /api/docs
Route::get('/api/docs/openapi.yaml', function (Request $request) {
    $path = base_path('docs/openapi.yaml');
    abort_unless(File::isFile($path), 404);

    $yaml = File::get($path);
    $apiBaseUrl = rtrim($request->getSchemeAndHttpHost().$request->getBaseUrl(), '/').'/api';
    $yaml = str_replace('__API_BASE_URL__', $apiBaseUrl, $yaml);

    return response($yaml, 200, [
        'Content-Type' => 'application/yaml; charset=UTF-8',
    ]);
})->name('api.docs.openapi');

Route::get('/api/docs', function () {
    return response()->view('api.docs', [
        'specUrl' => url('/api/docs/openapi.yaml'),
    ]);
})->name('api.docs');

Route::view('/api-docs', 'api-docs')->name('api-docs');

// Ana Sayfa
Route::get('/', [PageController::class, 'home'])->name('home');

// Mevcut Route::view('/baskan', ...) satırını silip yerine şunu yapıştırın:
Route::get('/baskan', function () {
    // Veritabanından aktif başkanı çek, yoksa boş bir nesne oluştur
    // (Böylece sayfa hata vermeden açılır)
    $mayor = Mayor::where('is_active', true)->first() ?? new Mayor;

    return view('pages.baskan', compact('mayor'));
})->name('baskan');

Route::get('/meclis', [PageController::class, 'meclis'])->name('meclis');

// Metod ismi 'mudurlukler' değil, 'mudurler' olmalı.
Route::get('/mudurler', [PageController::class, 'mudurler'])->name('mudurler');

// Müdürlük Detay Sayfası
Route::get('/mudurluk/{slug}', [PageController::class, 'mudurlukDetay'])->name('mudurluk.detay');

Route::get('/duyurular', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/duyurular/{slug}', [AnnouncementController::class, 'show'])->name('announcement.show');
Route::get('/faaliyet-raporlari', function () {
    $reports = ActivityReport::query()
        ->where('is_active', true)
        ->orderByDesc('year')
        ->orderByDesc('id')
        ->get();

    return view('pages.faaliyet-raporlari', compact('reports'));
})->name('activity-reports.index');
Route::get('/stratejik-plan', function () {
    $plans = StrategicPlan::query()
        ->where('is_active', true)
        ->orderByDesc('year')
        ->orderByDesc('id')
        ->get();

    return view('pages.stratejik-plan', compact('plans'));
})->name('strategic-plans.index');

Route::get('/seffaflik-hesap-verilebilirlik/{section?}', [TransparencyController::class, 'show'])
    ->name('transparency.show');

Route::get('/haberler', function () {
    return redirect()->route('announcements.index', ['tip' => 'duyuru']);
})->name('news.index');
Route::get('/ara', [SearchController::class, 'index'])->name('search');
Route::redirect('/arama', '/ara', 301);
Route::get('/haber/{slug}', [NewsController::class, 'show'])->name('news.detail');

Route::get('/sayfa/{slug}', [App\Http\Controllers\Frontend\PageController::class, 'show'])->name('page.detail');

Route::get('/meclis-uyeleri', function () {
    return redirect()->route('meclis', status: 301);
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

// İç görev paneli herkese açık olmamalı
Route::get('/gorev', function () {
    abort(404);
})->name('gorev');

Route::get('/rehber', function () {
    $directorates = \App\Models\Directorate::query()
        ->orderBy('name')
        ->get(['name', 'slug', 'manager_name', 'phone', 'email']);

    return view('pages.rehber', compact('directorates'));
})->name('rehber');

// İç URL'den dış e-belediye adresine yönlendirme (301).
Route::redirect('/e-belediye', 'https://e-belediye.kirklareli.bel.tr', 301);
Route::redirect('/e-belediye/', 'https://e-belediye.kirklareli.bel.tr', 301);

// Bazı ortamlarda uygulama /public altından servis edildiği için.
Route::redirect('/public/e-belediye', 'https://e-belediye.kirklareli.bel.tr', 301);
Route::redirect('/public/e-belediye/', 'https://e-belediye.kirklareli.bel.tr', 301);

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/contact-messages/{contactMessage}/print', \App\Http\Controllers\Admin\ContactMessagePrintController::class)
        ->name('admin.contact-messages.print');
});
