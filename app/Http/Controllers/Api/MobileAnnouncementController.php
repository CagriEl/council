<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AnnouncementDetailResource;
use App\Http\Resources\Api\AnnouncementListResource;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobileAnnouncementController extends Controller
{
    private const TYPES = ['duyuru', 'resmi', 'ihale'];

    /**
     * Sayfalanmış duyuru listesi.
     *
     * Query: per_page (1–50, varsayılan 15), page, tip (duyuru|resmi|ihale, opsiyonel)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);
        $tip = $request->query('tip');
        $type = in_array($tip, self::TYPES, true) ? $tip : null;

        $query = Announcement::query()
            ->publishedForPublic()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        return AnnouncementListResource::collection($query->paginate($perPage)->withQueryString());
    }

    /**
     * Slug ile duyuru detayı.
     */
    public function show(string $slug): AnnouncementDetailResource
    {
        $announcement = Announcement::query()
            ->publishedForPublic()
            ->with('galleryImages')
            ->where('slug', $slug)
            ->firstOrFail();

        return new AnnouncementDetailResource($announcement);
    }
}
