<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Doğru Controller'ı çağırdığımızdan emin oluyoruz:
use App\Http\Controllers\Api\ContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| URL: POST /api/contact/submit
|
*/

// Test Rotası (Kontrol için)
Route::get('/test', function () {
    return response()->json(['status' => 'OK', 'message' => 'API Calisiyor']);
});

// İletişim Formu Rotası
Route::post('/contact/submit', [ContactController::class, 'submit'])
    ->name('api.contact.submit');