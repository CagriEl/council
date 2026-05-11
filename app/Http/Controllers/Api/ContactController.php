<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $requestId = (string) Str::uuid();
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:100'],
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

        $platform = $request->header('X-Platform', 'web');
        $source = $validated['source'] ?? 'genel';

        try {
            $serviceRequest = ServiceRequest::query()->create([
                'full_name' => (string) $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'subject' => (string) ($validated['subject'] ?? 'Genel İstek / Öneri'),
                'description' => (string) $validated['message'],
                'status' => 'open',
                'source' => $source,
                'platform' => $platform,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Talebiniz oluşturuldu.',
                'tracking_no' => $serviceRequest->tracking_no,
                'request_id' => $requestId,
            ], 200);
        } catch (Throwable $e) {
            Log::warning('Contact submit failed', [
                'request_id' => $requestId,
                'ip' => $request->ip(),
                'source' => $source,
            ]);

            return $this->errorResponse(
                message: 'İşlem şu anda tamamlanamıyor. Lütfen daha sonra tekrar deneyin.',
                errorCode: 'UNKNOWN_ERROR',
                statusCode: 500,
                requestId: $requestId
            );
        }
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
    ) {
        return response()->json(array_merge([
            'status' => 'error',
            'message' => $message,
            'error_code' => $errorCode,
            'request_id' => $requestId,
        ], $extra), $statusCode);
    }
}
