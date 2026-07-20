<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MobilePushTokenController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string', 'max:255', 'regex:/^ExponentPushToken\[/'],
            'platform' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz push token.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $validator->validated();

        PushToken::query()->updateOrCreate(
            ['token' => $payload['token']],
            [
                'platform' => $payload['platform'] ?? null,
                'last_seen_at' => now(),
            ],
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Push token kaydedildi.',
        ]);
    }

    public function unregister(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz istek.',
            ], 422);
        }

        PushToken::query()
            ->where('token', $validator->validated()['token'])
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Push token silindi.',
        ]);
    }
}
