<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DirectorateDetailResource;
use App\Http\Resources\Api\DirectorateListResource;
use App\Models\Announcement;
use App\Models\Directorate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobileDirectorateController extends Controller
{
    /**
     * Tüm müdürlükler (slug ile detay sayfasına gidebilirsiniz).
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Directorate::query()
            ->with('vicePresident')
            ->orderBy('name');

        return DirectorateListResource::collection($query->get());
    }

    public function show(string $slug): DirectorateDetailResource
    {
        $directorate = Directorate::query()
            ->where('slug', $slug)
            ->with('vicePresident')
            ->firstOrFail();

        $directorate->setRelation(
            'latestAnnouncements',
            Announcement::query()
                ->publishedForPublic()
                ->latest('date')
                ->take(5)
                ->get()
        );

        return new DirectorateDetailResource($directorate);
    }
}
