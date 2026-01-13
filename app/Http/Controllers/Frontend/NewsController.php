<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News; // Haber Modeliniz
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // ... index metodu vb. varsa burada durabilir ...

    public function show($slug)
    {
        // Haberi slug'a göre bul
        $news = News::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Haber detay sayfasına gönder (View adını aşağıda oluşturacağız)
        return view('pages.news_detail', compact('news'));
    }
}