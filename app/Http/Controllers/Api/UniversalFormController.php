<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UniversalForm;
use App\Services\CloudflareTurnstile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UniversalFormController extends Controller
{
    public function submit(Request $request, CloudflareTurnstile $turnstile): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source' => ['nullable', 'string', 'min:2', 'max:120'],
            'platform' => ['nullable', 'string', 'max:50'],
            'form_data' => ['nullable', 'array', 'min:1'],
            'form_data.*' => ['nullable', 'string', 'max:5000'],
            'website' => ['nullable', 'string', 'max:10'],
            'company_url' => ['nullable', 'string', 'max:200'],
            'cf-turnstile-response' => ['nullable', 'string', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gönderilen veriler doğrulanamadı.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ((string) $request->input('website', '') !== ''
            || (string) $request->input('company_url', '') !== '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz istek.',
            ], 422);
        }

        $platform = mb_strtolower(mb_substr((string) $request->header('X-Platform', $request->input('platform', 'web')), 0, 50));

        if (! $turnstile->isMobilePlatform($platform) && $turnstile->enabled()) {
            $token = (string) $request->input('cf-turnstile-response', '');
            if (! $turnstile->verify($token, $request->ip())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Güvenlik doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.',
                ], 422);
            }
        }

        $source = (string) $request->input('source', 'bilinmiyor');
        $data = $request->input('form_data');

        if (! is_array($data) || $data === []) {
            $data = $request->except([
                'source',
                'platform',
                '_token',
                'website',
                'company_url',
                'cf-turnstile-response',
            ]);
        }

        if ($data === []) {
            return response()->json([
                'status' => 'error',
                'message' => 'Veri gönderilmedi.',
            ], 422);
        }

        try {
            UniversalForm::create([
                'source' => $source,
                'platform' => $platform !== '' ? $platform : 'web',
                'ip_address' => $request->ip(),
                'data' => $data,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Mesaj başarıyla alındı.'], 200);
        } catch (Throwable) {
            return response()->json(['status' => 'error', 'message' => 'Sunucu hatası.'], 500);
        }
    }
}
