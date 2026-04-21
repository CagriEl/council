<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    private const CATEGORIES = ['belediye', 'kultur', 'spor', 'cevre', 'sosyal'];

    public function index(Request $request): View
    {
        $kategori = $request->query('kategori');
        $currentCategory = in_array($kategori, self::CATEGORIES, true) ? $kategori : null;

        $query = News::query()
            ->publishedForPublic()
            ->when($currentCategory, fn ($q) => $q->where('category', $currentCategory))
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        $news = $query->paginate(12)->withQueryString();

        return view('pages.haberler', compact('news', 'currentCategory'));
    }

    public function show(string $slug): View
    {
        $news = News::query()
            ->publishedForPublic()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.news_detail', compact('news'));
    }
}
