<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UniversalForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class UniversalFormController extends Controller
{
    public function submit(Request $request)
    {
        $requestId = (string) Str::uuid();
        $validator = Validator::make($request->all(), [
            '*' => ['nullable'],
        ]);

        if ($validator->fails() || count($request->all()) === 0) {
            return $this->errorResponse(
                message: 'Gönderilen bilgiler geçersiz.',
                errorCode: 'VALIDATION_FAILED',
                statusCode: 422,
                requestId: $requestId
            );
        }

        $platform = $request->header('X-Platform', $request->input('platform', 'web'));
        $source = $request->input('source', 'bilinmiyor');

        try {
            UniversalForm::create([
                'source' => $source,
                'platform' => $platform,
                'ip_address' => $request->ip(),
                'data' => $request->except(['source', 'platform', '_token']),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Mesaj başarıyla alındı.',
                'request_id' => $requestId,
            ], 200);
        } catch (Throwable $e) {
            Log::warning('Universal form submit failed', [
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

    protected function errorResponse(string $message, string $errorCode, int $statusCode, string $requestId)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'error_code' => $errorCode,
            'request_id' => $requestId,
        ], $statusCode);
    }
}
