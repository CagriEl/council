<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormSubmitted;
use App\Models\ContactMessage;
use App\Services\CloudflareTurnstile;
use App\Services\ContactSpamGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ContactController extends Controller
{
    public function submit(
        Request $request,
        CloudflareTurnstile $turnstile,
        ContactSpamGuard $spamGuard,
    ): JsonResponse {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['nullable', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'source' => ['nullable', 'string', 'max:80'],
            'website' => ['nullable', 'string', 'max:10'],
            'company_url' => ['nullable', 'string', 'max:200'], // honeypot
            'cf-turnstile-response' => ['nullable', 'string', 'max:2048'],
            'photo' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gönderilen veriler doğrulanamadı.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Honeypot: botlar doldurursa sessizce reddet
        if ((string) $request->input('website', '') !== ''
            || (string) $request->input('company_url', '') !== '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Geçersiz istek.',
            ], 422);
        }

        $platform = mb_strtolower(mb_substr((string) $request->header('X-Platform', 'web'), 0, 50));
        $isMobileApp = $turnstile->isMobilePlatform($platform);

        // Web formlarında Cloudflare Turnstile zorunlu (TURNSTILE_SECRET tanımlıysa)
        if (! $isMobileApp && $turnstile->enabled()) {
            $token = (string) $request->input('cf-turnstile-response', '');
            if (! $turnstile->verify($token, $request->ip())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Güvenlik doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.',
                ], 422);
            }
        }

        $payload = $validator->validated();
        if ($spamGuard->isBlocked(
            $payload['name'] ?? null,
            $payload['email'] ?? null,
            $payload['subject'] ?? null,
            $payload['message'] ?? null,
            $payload['phone'] ?? null,
        )) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mesajınız güvenlik kontrolünden geçemedi. Lütfen düz metin kullanın.',
            ], 422);
        }

        $source = (string) $request->input('source', 'genel');
        $platform = $platform !== '' ? $platform : 'web';

        try {
            unset(
                $payload['source'],
                $payload['website'],
                $payload['company_url'],
                $payload['cf-turnstile-response'],
                $payload['photo']
            );

            if ($request->hasFile('photo')) {
                $storedPath = $request->file('photo')->store('contact-photos', 'public');
                $payload['photo_url'] = Storage::disk('public')->url($storedPath);
            }

            ContactMessage::create([
                'platform' => $platform,
                'source' => $source,
                'ip_address' => $request->ip(),
                'payload' => $payload,
            ]);

            $this->notifyInbox($platform, $source, $payload, $request->ip());

            return response()->json(['status' => 'success', 'message' => 'Mesaj alındı.'], 200);
        } catch (Throwable) {
            return response()->json(['status' => 'error', 'message' => 'Sunucu hatası.'], 500);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function notifyInbox(string $platform, string $source, array $payload, ?string $ip): void
    {
        $to = (string) config('services.contact.notify_to', 'kirklareli@kirklareli.bel.tr');
        if (! filled($to)) {
            return;
        }

        try {
            Mail::to($to)->send(new ContactFormSubmitted([
                'platform' => $platform,
                'source' => $source,
                'name' => (string) ($payload['name'] ?? ''),
                'phone' => isset($payload['phone']) ? (string) $payload['phone'] : null,
                'email' => isset($payload['email']) ? (string) $payload['email'] : null,
                'subject' => isset($payload['subject']) ? (string) $payload['subject'] : null,
                'message' => (string) ($payload['message'] ?? ''),
                'photo_url' => isset($payload['photo_url']) ? (string) $payload['photo_url'] : null,
                'ip_address' => $ip,
            ]));
        } catch (Throwable $e) {
            // Form kaydı başarılı kalsın; mail hatası kullanıcıya yansımasın
            Log::error('Contact form mail gönderilemedi', [
                'message' => $e->getMessage(),
                'to' => $to,
            ]);
        }
    }
}
