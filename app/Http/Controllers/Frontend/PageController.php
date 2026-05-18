<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        // Slug'a göre sayfayı bul, aktif değilse 404 ver
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // View dosyasına gönder
        return view('pages.general_page', compact('page'));
    }
}