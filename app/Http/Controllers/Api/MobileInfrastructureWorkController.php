<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Mobil altyapı çalışmaları listesi.
 * Veri kaynağı: config/infrastructure_works.php (panel modülü gelince DB'ye taşınacak).
 */
class MobileInfrastructureWorkController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => config('infrastructure_works', []),
        ]);
    }
}
