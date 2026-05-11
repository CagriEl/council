<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Services\AnnouncementScraperService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class AnnouncementController extends Controller
{
    /**
     * HTML listesinden çekilen resmî duyurular (önbellek: 1 saat).
     */
    public function index(Request $request, AnnouncementScraperService $scraper): AnonymousResourceCollection
    {
        $sourceUrl = $request->query('source_url');
        if (! is_string($sourceUrl) || trim($sourceUrl) === '') {
            $sourceUrl = $scraper->defaultScraperUrl();
        }

        $refresh = filter_var($request->query('refresh', false), FILTER_VALIDATE_BOOL);
        $limit = min(max((int) $request->query('limit', 50), 1), 200);

        $cacheKey = 'api.announcements.official.scraped.'.sha1($sourceUrl);
        if ($refresh) {
            Cache::forget($cacheKey);
        }

        $items = Cache::remember($cacheKey, 3600, function () use ($scraper, $sourceUrl) {
            return $scraper->fetchOfficialList($sourceUrl);
        });
        $items = array_slice($items, 0, $limit);

        return AnnouncementResource::collection($items)->additional([
            'meta' => [
                'source_url' => $sourceUrl,
                'cached_ttl_seconds' => 3600,
                'refresh' => $refresh,
                'count' => count($items),
            ],
        ]);
    }
}
