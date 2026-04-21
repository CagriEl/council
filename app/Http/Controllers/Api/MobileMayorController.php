<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MayorResource;
use App\Models\Mayor;
use Illuminate\Http\JsonResponse;

class MobileMayorController extends Controller
{
    /**
     * Aktif belediye başkanı kaydı (panelde işaretli veya tek kayıt).
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
