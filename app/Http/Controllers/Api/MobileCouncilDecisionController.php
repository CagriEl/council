<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CouncilDecisionResource;
use App\Models\CouncilDecision;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobileCouncilDecisionController extends Controller
{
    /**
     * Meclis kararları (PDF bağlantıları dahil).
     *
     * Query: per_page (1–50), page, year (isteğe bağlı filtre)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);
        $year = $request->query('year');

        $query = CouncilDecision::query()->orderByDesc('meeting_date');

        if ($year !== null && $year !== '' && is_numeric($year)) {
            $query->where('year', (int) $year);
        }

        return CouncilDecisionResource::collection($query->paginate($perPage)->withQueryString());
    }
}
