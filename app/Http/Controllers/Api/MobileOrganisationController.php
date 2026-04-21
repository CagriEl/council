<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrganisationTreeResource;
use App\Models\VicePresident;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobileOrganisationController extends Controller
{
    /**
     * Başkan yardımcıları ve bağlı müdürlükler (web’deki /mudurler şemasına paralel).
     */
    public function tree(): AnonymousResourceCollection
    {
        $query = VicePresident::query()
            ->with(['directorates' => fn ($q) => $q->orderBy('name')])
            ->orderBy('order');

        return OrganisationTreeResource::collection($query->get());
    }
}
