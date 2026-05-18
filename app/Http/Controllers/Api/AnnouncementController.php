<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Services\AnnouncementScraperService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class AnnouncementController extends Controller
{
    /**
     * HTML listesinden çekilen resmî duyurular (önbellek: 1 saat).
     */
    public function index(AnnouncementScraperService $scraper): AnonymousResourceCollection
    {
        $items = Cache::remember('api.announcements.official.scraped', 3600, function () use ($scraper) {
            return $scraper->fetchOfficialList();
        });

        return AnnouncementResource::collection($items);
    }
}
