<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MobileTransportScheduleController extends Controller
{
    /**
     * Mobil uygulama sefer saatleri ekranı için güzergah listesi.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => config('transport_schedules', []),
        ]);
    }
}
