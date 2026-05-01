<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        if (! $request->has('name') || ! $request->has('message')) {
            return response()->json(['status' => 'error', 'message' => 'Eksik veri.'], 400);
        }

        $platform = $request->header('X-Platform', 'web');
        $source = $request->input('source', 'genel');

        try {
            $serviceRequest = ServiceRequest::query()->create([
                'full_name' => (string) $request->input('name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'subject' => (string) $request->input('subject', 'Genel İstek / Öneri'),
                'description' => (string) $request->input('message'),
                'status' => 'open',
                'source' => $source,
                'platform' => $platform,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Talebiniz oluşturuldu.',
                'tracking_no' => $serviceRequest->tracking_no,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Sunucu hatası.'], 500);
        }
    }
}