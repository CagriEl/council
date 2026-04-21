<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NewsDetailResource;
use App\Http\Resources\Api\NewsListResource;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobileNewsController extends Controller
{
    private const CATEGORIES = ['belediye', 'kultur', 'spor', 'cevre', 'sosyal'];

    /**
     * Sayfalanmış haber listesi (yalnızca ön yüzde yayında olanlar).
     *
     * Query: per_page (1–50, varsayılan 15), page, kategori (opsiyonel)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);
        $kategori = $request->query('kategori');
        $category = in_array($kategori, self::CATEGORIES, true) ? $kategori : null;

        $query = News::query()
            ->publishedForPublic()
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        return NewsListResource::collection($query->paginate($perPage)->withQueryString());
    }

    /**
     * Slug ile haber detayı.
     */
    public function show(string $slug): NewsDetailResource
    {
        $news = News::query()
            ->publishedForPublic()
            ->where('slug', $slug)
            ->firstOrFail();

        return new NewsDetailResource($news);
    }
}
