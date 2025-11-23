<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/meclis', [PageController::class, 'meclis'])->name('meclis');
Route::get('/baskan', [PageController::class, 'baskan'])->name('baskan');
Route::get('/mudurluk/{slug}', [PageController::class, 'mudurlukDetay'])->name('mudurluk.detay');
Route::get('/mudurlukler', [PageController::class, 'mudurlukler'])->name('mudurlukler');