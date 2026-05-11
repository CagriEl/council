<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class ServiceRequestController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $requestId = (string) Str::uuid();
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return $this->errorResponse(
                message: 'Gönderilen bilgiler geçersiz.',
                errorCode: 'VALIDATION_FAILED',
                statusCode: 422,
                requestId: $requestId,
                extra: ['errors' => $validator->errors()]
            );
        }

        $validated = $validator->validated();
        $platform = $request->header('X-Platform', $validated['platform'] ?? 'web');
        $fullName = trim((string) ($validated['full_name'] ?? $validated['name'] ?? ''));
        $subject = trim((string) ($validated['subject'] ?? 'Genel İstek / Öneri'));
        $description = trim((string) ($validated['description'] ?? $validated['message'] ?? ''));

        if ($fullName === '' || $description === '') {
            return $this->errorResponse(
                message: 'Ad soyad ve mesaj alanları zorunludur.',
                errorCode: 'VALIDATION_FAILED',
                statusCode: 422,
                requestId: $requestId
            );
        }

        try {
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
                'request_id' => $requestId,
            ]);
        } catch (Throwable $e) {
            Log::warning('Service request submit failed', [
                'request_id' => $requestId,
                'ip' => $request->ip(),
            ]);

            return $this->errorResponse(
                message: 'İşlem şu anda tamamlanamıyor. Lütfen daha sonra tekrar deneyin.',
                errorCode: 'UNKNOWN_ERROR',
                statusCode: 500,
                requestId: $requestId
            );
        }
    }

    public function track(string $trackingNo): JsonResponse
    {
        $requestId = (string) Str::uuid();
        $serviceRequest = ServiceRequest::query()
            ->publicStatuses()
            ->where('tracking_no', $trackingNo)
            ->first();

        if (! $serviceRequest) {
            return $this->errorResponse(
                message: 'Takip numarası bulunamadı.',
                errorCode: 'NOT_FOUND',
                statusCode: 404,
                requestId: $requestId
            );
        }

        return response()->json([
            'status' => 'success',
            'request_id' => $requestId,
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

    /**
     * @param  array<string, mixed>  $extra
     */
    protected function errorResponse(
        string $message,
        string $errorCode,
        int $statusCode,
        string $requestId,
        array $extra = []
    ): JsonResponse {
        return response()->json(array_merge([
            'status' => 'error',
            'message' => $message,
            'error_code' => $errorCode,
            'request_id' => $requestId,
        ], $extra), $statusCode);
    }
}
