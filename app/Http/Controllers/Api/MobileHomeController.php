<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AnnouncementListResource;
use App\Http\Resources\Api\MayorResource;
use App\Http\Resources\Api\NewsListResource;
use App\Http\Resources\Api\QuickLinkResource;
use App\Http\Resources\Api\SliderResource;
use App\Models\Announcement;
use App\Models\Mayor;
use App\Models\News;
use App\Models\QuickLink;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileHomeController extends Controller
{
    private const MODULES = [
        'sliders',
        'quick_links',
        'mayor',
        'headline_news',
        'announcements_by_type',
    ];

    /**
     * Ana sayfaya yakın özet paketi. Mobil uygulama tek istekle açılış ekranını doldurabilir.
     *
     * Query: include — virgülle ayrılmış modül listesi. Boş veya * ise hepsi.
     * Örnek: `?include=sliders,mayor,headline_news`
     */
    public function index(Request $request): JsonResponse
    {
        $include = $this->resolveInclude($request->query('include'));

        $payload = [];

        if (in_array('sliders', $include, true)) {
            $sliders = Slider::query()
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
            $payload['sliders'] = SliderResource::collection($sliders)->resolve();
        }

        if (in_array('quick_links', $include, true)) {
            $links = QuickLink::query()->orderBy('order')->get();
            $payload['quick_links'] = QuickLinkResource::collection($links)->resolve();
        }

        if (in_array('mayor', $include, true)) {
            $mayor = Mayor::query()->where('is_active', true)->first()
                ?? Mayor::query()->first();
            $payload['mayor'] = $mayor ? (new MayorResource($mayor))->resolve() : null;
        }

        if (in_array('headline_news', $include, true)) {
            $headlines = News::query()
                ->publishedForPublic()
                ->where('is_headline', true)
                ->latest('published_at')
                ->take(5)
                ->get();
            $payload['headline_news'] = NewsListResource::collection($headlines)->resolve();
        }

        if (in_array('announcements_by_type', $include, true)) {
            $payload['announcements_by_type'] = [
                'duyuru' => AnnouncementListResource::collection(
                    Announcement::query()->publishedForPublic()->where('type', 'duyuru')->latest('date')->take(5)->get()
                )->resolve(),
                'resmi' => AnnouncementListResource::collection(
                    Announcement::query()->publishedForPublic()->where('type', 'resmi')->latest('date')->take(5)->get()
                )->resolve(),
                'ihale' => AnnouncementListResource::collection(
                    Announcement::query()->publishedForPublic()->where('type', 'ihale')->latest('date')->take(5)->get()
                )->resolve(),
            ];
        }

        return response()->json($payload);
    }

    /**
     * @return list<string>
     */
    private function resolveInclude(mixed $raw): array
    {
        if ($raw === null || $raw === '' || $raw === '*') {
            return self::MODULES;
        }

        $requested = array_map('trim', explode(',', (string) $raw));
        $requested = array_map('strtolower', $requested);
        $intersect = array_values(array_intersect($requested, self::MODULES));

        return $intersect !== [] ? $intersect : self::MODULES;
    }
}
