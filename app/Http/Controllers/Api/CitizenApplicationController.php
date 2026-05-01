<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CitizenApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CitizenApplicationController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_type' => ['required', Rule::in(['ruhsat', 'e_imar', 'evrak', 'sosyal_destek'])],
            'full_name' => ['required', 'string', 'max:255'],
            'identity_no' => ['nullable', 'digits:11'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'request_summary' => ['required', 'string', 'max:5000'],
            'source' => ['nullable', 'string', 'max:100'],
            'platform' => ['nullable', 'string', 'max:50'],
        ]);

        $platform = $request->header('X-Platform', $validated['platform'] ?? 'web');

        $application = CitizenApplication::query()->create([
            'service_type' => $validated['service_type'],
            'full_name' => $validated['full_name'],
            'identity_no' => $validated['identity_no'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'request_summary' => $validated['request_summary'],
            'status' => 'received',
            'source' => $validated['source'] ?? 'e-hizmet-basvuru',
            'platform' => $platform,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Başvurunuz alındı.',
            'tracking_no' => $application->tracking_no,
        ]);
    }

    public function track(string $trackingNo): JsonResponse
    {
        $application = CitizenApplication::query()
            ->where('tracking_no', $trackingNo)
            ->first();

        if (! $application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Takip numarası bulunamadı.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'tracking_no' => $application->tracking_no,
                'service_type' => $application->service_type,
                'full_name' => $application->full_name,
                'status' => $application->status,
                'submitted_at' => optional($application->created_at)->toDateTimeString(),
                'resolved_at' => optional($application->resolved_at)->toDateTimeString(),
                'response' => $application->response_text,
            ],
        ]);
    }
}
