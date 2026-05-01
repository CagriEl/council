<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'message' => ['nullable', 'string', 'max:5000'],
            'source' => ['nullable', 'string', 'max:100'],
            'platform' => ['nullable', 'string', 'max:50'],
        ]);

        $platform = $request->header('X-Platform', $validated['platform'] ?? 'web');
        $fullName = trim((string) ($validated['full_name'] ?? $validated['name'] ?? ''));
        $subject = trim((string) ($validated['subject'] ?? 'Genel İstek / Öneri'));
        $description = trim((string) ($validated['description'] ?? $validated['message'] ?? ''));

        if ($fullName === '' || $description === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ad soyad ve mesaj alanları zorunludur.',
            ], 422);
        }

        $serviceRequest = ServiceRequest::query()->create([
            'full_name' => $fullName,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'subject' => $subject,
            'description' => $description,
            'status' => 'open',
            'source' => $validated['source'] ?? 'talep-sikayet',
            'platform' => $platform,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Talebiniz oluşturuldu.',
            'tracking_no' => $serviceRequest->tracking_no,
        ]);
    }

    public function track(string $trackingNo): JsonResponse
    {
        $serviceRequest = ServiceRequest::query()
            ->publicStatuses()
            ->where('tracking_no', $trackingNo)
            ->first();

        if (! $serviceRequest) {
            return response()->json([
                'status' => 'error',
                'message' => 'Takip numarası bulunamadı.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'tracking_no' => $serviceRequest->tracking_no,
                'full_name' => $serviceRequest->full_name,
                'subject' => $serviceRequest->subject,
                'current_status' => $serviceRequest->status,
                'submitted_at' => optional($serviceRequest->created_at)->toDateTimeString(),
                'resolved_at' => optional($serviceRequest->resolved_at)->toDateTimeString(),
                'response' => $serviceRequest->response_text,
            ],
        ]);
    }
}
