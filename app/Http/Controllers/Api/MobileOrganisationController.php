<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DirectorateListResource;
use App\Http\Resources\Api\OrganisationTreeResource;
use App\Models\Directorate;
use App\Models\VicePresident;
use Illuminate\Http\JsonResponse;

class MobileOrganisationController extends Controller
{
    /**
     * Belediye başkanına doğrudan bağlı müdürlükler + başkan yardımcıları ve müdürlükleri
     * (web’deki /mudurler şemasına paralel).
     */
    public function tree(): JsonResponse
    {
        $mayorDirectorates = Directorate::query()
            ->whereNull('vice_president_id')
            ->orderBy('name')
            ->get();

        $vicePresidents = VicePresident::query()
            ->with(['directorates' => fn ($q) => $q->orderBy('name')])
            ->orderBy('order')
            ->get();

        return response()->json([
            'mayor_directorates' => DirectorateListResource::collection($mayorDirectorates)->resolve(),
            'data' => OrganisationTreeResource::collection($vicePresidents)->resolve(),
        ]);
    }
}
