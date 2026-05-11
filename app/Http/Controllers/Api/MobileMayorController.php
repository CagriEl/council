<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MayorResource;
use App\Models\Mayor;
use Illuminate\Http\JsonResponse;

class MobileMayorController extends Controller
{
    /**
     * Başkan sayfası verisi (JSON). Rotalar: GET /api/mayor, GET /api/baskan.
     *
     * Önce `is_active = true` kayıt; yoksa ilk kayıt.
     */
    public function show(): JsonResponse|MayorResource
    {
        $mayor = Mayor::query()->where('is_active', true)->first()
            ?? Mayor::query()->first();

        if (! $mayor) {
            return response()->json(['message' => 'Başkan kaydı bulunamadı.'], 404);
        }

        return new MayorResource($mayor);
    }
}
