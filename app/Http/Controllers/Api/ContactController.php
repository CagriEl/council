<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ContactController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['nullable', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:3', 'max:5000'],
            'source' => ['nullable', 'string', 'max:80'],
            'website' => ['nullable', 'string', 'max:10'],
            'photo' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gönderilen veriler doğrulanamadı.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Honeypot field for basic bot filtering.
        if ((string) $request->input('website', '') !== '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz istek.',
            ], 422);
        }

        $platform = mb_substr((string) $request->header('X-Platform', 'web'), 0, 50);
        $source = (string) $request->input('source', 'genel');

        try {
            $payload = $validator->validated();
            unset($payload['source'], $payload['website'], $payload['photo']);

            if ($request->hasFile('photo')) {
                $storedPath = $request->file('photo')->store('contact-photos', 'public');
                $payload['photo_url'] = Storage::disk('public')->url($storedPath);
            }

            ContactMessage::create([
                'platform' => $platform,
                'source'   => $source,
                'ip_address' => $request->ip(),
                'payload'  => $payload,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Mesaj alındı.'], 200);
        } catch (Throwable) {
            return response()->json(['status' => 'error', 'message' => 'Sunucu hatası.'], 500);
        }
    }
}