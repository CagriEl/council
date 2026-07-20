<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UniversalForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UniversalFormController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'source' => ['nullable', 'string', 'min:2', 'max:120'],
            'platform' => ['nullable', 'string', 'max:50'],
            'form_data' => ['nullable', 'array', 'min:1'],
            'form_data.*' => ['nullable', 'string', 'max:5000'],
            'website' => ['nullable', 'string', 'max:10'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gönderilen veriler doğrulanamadı.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ((string) $request->input('website', '') !== '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz istek.',
            ], 422);
        }

        $platform = mb_substr((string) $request->header('X-Platform', $request->input('platform', 'web')), 0, 50);
        $source = (string) $request->input('source', 'bilinmiyor');
        $data = $request->input('form_data');

        if (!is_array($data) || $data === []) {
            $data = $request->except(['source', 'platform', '_token', 'website']);
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
                'platform' => $platform,
                'ip_address' => $request->ip(),
                'data' => $data,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Mesaj başarıyla alındı.'], 200);
        } catch (Throwable) {
            return response()->json(['status' => 'error', 'message' => 'Sunucu hatası.'], 500);
        }
    }
}
