<?php

use App\Http\Controllers\Frontend\AnnouncementController;
use App\Http\Controllers\Frontend\NewsController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\PageController;
use App\Models\CouncilDecision;
use App\Models\Mayor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// API dokümantasyonu (Swagger UI) — tarayıcı: /api/docs
Route::get('/api/docs/openapi.yaml', function () {
    $path = base_path('docs/openapi.yaml');
    abort_unless(File::isFile($path), 404);

    return response()->file($path, [
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

// Başkan sayfası
Route::get('/baskan', function () {
    $mayor = Mayor::where('is_active', true)->first() ?? new Mayor;

    return view('pages.baskan', compact('mayor'));
})->name('baskan');

Route::get('/meclis', [PageController::class, 'meclis'])->name('meclis');
Route::get('/mudurler', [PageController::class, 'mudurler'])->name('mudurler');
Route::get('/mudurluk/{slug}', [PageController::class, 'mudurlukDetay'])->name('mudurluk.detay');

Route::get('/duyurular', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/duyurular/{slug}', [AnnouncementController::class, 'show'])->name('announcement.show');

Route::get('/haberler', [NewsController::class, 'index'])->name('news.index');
Route::get('/ara', [SearchController::class, 'index'])->name('search');
Route::get('/haber/{slug}', [NewsController::class, 'show'])->name('news.detail');

Route::get('/sayfa/{slug}', [App\Http\Controllers\Frontend\PageController::class, 'show'])->name('page.detail');

// Eski URL desteği: tek meclis sayfasına yönlendirme
Route::redirect('/meclis-uyeleri', '/meclis', 301)->name('council.index');

Route::get('/meclis-kararlari', function () {
    $currentYear = date('Y');
    $years = range($currentYear, $currentYear - 5);

    $decisions = CouncilDecision::orderBy('meeting_date', 'desc')->get();

    return view('pages.meclis-kararlari', compact('years', 'decisions'));
})->name('meclis-kararlari');

Route::get('/iletisim', function () {
    return view('pages.iletisim');
})->name('iletisim');

Route::get('/gorev', function () {
    return view('pages.gorev');
})->name('gorev');

Route::get('/rehber', function () {
    return view('pages.rehber');
})->name('rehber');

Route::get('/e-belediye', function () {
    return view('pages.e-belediye');
})->name('e-services');

Route::redirect('/talep-sikayet', '/iletisim', 301)->name('service-requests.page');

Route::get('/e-hizmetler/basvuru', function () {
    return view('pages.e-hizmet-basvuru');
})->name('citizen-applications.page');

Route::get('/hizmetler/yakinda', function () {
    $module = request()->query('modul', 'Bu hizmet');

    return view('pages.coming-soon', compact('module'));
})->name('coming-soon');
