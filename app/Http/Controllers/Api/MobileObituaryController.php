<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ObituaryResource;
use App\Models\Obituary;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobileObituaryController extends Controller
{
    /**
     * Aktif vefat kayıtları listesi.
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Obituary::query()
            ->active()
            ->orderBy('sort_order')
            ->orderByDesc('death_date')
            ->orderBy('full_name');

        return ObituaryResource::collection($query->get());
    }
}
