<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MobileAnnouncementController;
use App\Http\Controllers\Api\MobileCouncilDecisionController;
use App\Http\Controllers\Api\MobileCouncilMemberController;
use App\Http\Controllers\Api\MobileDirectorateController;
use App\Http\Controllers\Api\MobileHomeController;
use App\Http\Controllers\Api\MobileMayorController;
use App\Http\Controllers\Api\MobileMenuController;
use App\Http\Controllers\Api\MobileNewsController;
use App\Http\Controllers\Api\MobileOrganisationController;
use App\Http\Controllers\Api\MobilePageController;
use App\Http\Controllers\Api\UniversalFormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Mobil uygulama (JSON). Haber/duyuru: publishedForPublic. Görseller ve PDF:
| APP_URL + /storage/... (storage:link). Okuma uçları throttle:120,1.
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'Kırklareli Belediyesi JSON API',
        'documentation_url' => url('/api/docs'),
        'openapi_url' => url('/api/docs/openapi.yaml'),
        'try' => [
            'GET '.url('/api/test'),
            'GET '.url('/api/home'),
            'GET '.url('/api/news'),
        ],
    ]);
})->name('api.index');

Route::middleware('throttle:120,1')->group(function () {
    Route::get('/news', [MobileNewsController::class, 'index'])->name('api.news.index');
    Route::get('/news/{slug}', [MobileNewsController::class, 'show'])->name('api.news.show');
    Route::get('/announcements', [MobileAnnouncementController::class, 'index'])->name('api.announcements.index');
    Route::get('/announcements/official', [AnnouncementController::class, 'index'])->name('api.announcements.official');
    Route::get('/announcements/{slug}', [MobileAnnouncementController::class, 'show'])->name('api.announcements.show');

    Route::get('/home', [MobileHomeController::class, 'index'])->name('api.home');
    Route::get('/pages', [MobilePageController::class, 'index'])->name('api.pages.index');
    Route::get('/pages/{slug}', [MobilePageController::class, 'show'])->name('api.pages.show');
    Route::get('/menus', [MobileMenuController::class, 'index'])->name('api.menus.index');
    Route::get('/mayor', [MobileMayorController::class, 'show'])->name('api.mayor.show');
    Route::get('/council/members', [MobileCouncilMemberController::class, 'index'])->name('api.council.members');
    Route::get('/council/decisions', [MobileCouncilDecisionController::class, 'index'])->name('api.council.decisions');
    Route::get('/directorates', [MobileDirectorateController::class, 'index'])->name('api.directorates.index');
    Route::get('/directorates/{slug}', [MobileDirectorateController::class, 'show'])->name('api.directorates.show');
    Route::get('/organisation/tree', [MobileOrganisationController::class, 'tree'])->name('api.organisation.tree');
});

Route::get('/test', function () {
    return response()->json(['status' => 'OK', 'message' => 'API Calisiyor']);
});

Route::post('/contact/submit', [ContactController::class, 'submit'])
    ->name('api.contact.submit');

Route::post('/forms/submit', [UniversalFormController::class, 'submit'])
    ->name('api.forms.submit');
