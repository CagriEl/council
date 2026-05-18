<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PageDetailResource;
use App\Http\Resources\Api\PageListResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobilePageController extends Controller
{
    /**
     * Yönetim panelinden eklenen statik sayfalar (ör. /sayfa/{slug}).
     *
     * Query: per_page (1–50, varsayılan 15), page
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);

        $query = Page::query()
            ->where('is_active', true)
            ->orderBy('title');

        return PageListResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function show(string $slug): PageDetailResource
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return new PageDetailResource($page);
    }
}
