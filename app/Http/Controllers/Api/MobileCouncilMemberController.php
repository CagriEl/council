<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CouncilMemberResource;
use App\Models\CouncilMember;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobileCouncilMemberController extends Controller
{
    /**
     * Meclis üyeleri listesi (aktif kayıtlar, sıralı).
     */
    public function index(): AnonymousResourceCollection
    {
        $query = CouncilMember::query()
            ->with('politicalParty')
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name');

        return CouncilMemberResource::collection($query->get());
    }
}
