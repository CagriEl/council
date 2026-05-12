<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DebtQueryAudit;
use App\Services\EOdemeService;
use App\Services\TurnstileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class EOdemeController extends Controller
{
    public function __construct(
        protected EOdemeService $eOdemeService,
        protected TurnstileService $turnstileService
    ) {}

    public function borcSorgula(Request $request): JsonResponse
    {
        $requestId = (string) Str::uuid();
        $startedAt = microtime(true);
        $maskedMukellefNo = $this->maskIdentifier((string) $request->input('mukellef_no', ''));

        $validator = Validator::make($request->all(), [
            'mukellef_tipi' => ['required', 'string', 'in:SICIL,TCKN,VKN,SUABN,JEOABN'],
            'mukellef_no' => ['required', 'string', 'max:15'],
            'indirimli_odenecek_mi' => ['nullable', 'boolean'],
            'sadece_su_borclari' => ['nullable', 'boolean'],
            'borc_sorgu_kvkk_onay' => ['required', 'accepted'],
            'cf_turnstile_response' => $this->turnstileService->isEnabled()
                ? ['required', 'string']
                : ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            $this->recordAudit(
                requestId: $requestId,
                request: $request,
                maskedMukellefNo: $maskedMukellefNo,
                captchaOk: false,
                status: 'validation_failed',
                startedAt: $startedAt
            );

            return $this->errorResponse(
                message: 'Gönderilen bilgiler geçersiz.',
                errorCode: 'VALIDATION_FAILED',
                statusCode: 422,
                requestId: $requestId,
                extra: ['errors' => $validator->errors()]
            );
        }

        $validated = $validator->validated();

        $captchaOk = false;
        if ($this->turnstileService->isEnabled()) {
            try {
                $captchaOk = $this->turnstileService->verify(
                    (string) $validated['cf_turnstile_response'],
                    $request->ip()
                );
            } catch (RuntimeException $e) {
                $this->recordAudit(
                    requestId: $requestId,
                    request: $request,
                    maskedMukellefNo: $maskedMukellefNo,
                    captchaOk: false,
                    status: 'captcha_unavailable',
                    startedAt: $startedAt
                );

                return $this->errorResponse(
                    message: 'Güvenlik doğrulaması şu anda çalışmıyor. Lütfen daha sonra tekrar deneyin.',
                    errorCode: 'UNKNOWN_ERROR',
                    statusCode: 503,
                    requestId: $requestId
                );
            }

            if (! $captchaOk) {
                $this->recordAudit(
                    requestId: $requestId,
                    request: $request,
                    maskedMukellefNo: $maskedMukellefNo,
                    captchaOk: false,
                    status: 'captcha_failed',
                    startedAt: $startedAt
                );

                return $this->errorResponse(
                    message: 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.',
                    errorCode: 'CAPTCHA_FAILED',
                    statusCode: 422,
                    requestId: $requestId
                );
            }
        }

        try {
            $result = $this->eOdemeService->borcSorgula([
                'mukellef_tipi' => $validated['mukellef_tipi'],
                'mukellef_no' => $validated['mukellef_no'],
                'indirimli_odenecek_mi' => ! empty($validated['indirimli_odenecek_mi']) ? 1 : 0,
                'sadece_su_borclari' => ! empty($validated['sadece_su_borclari']) ? 1 : 0,
            ]);

            $maskedResult = $this->maskSensitiveData($result);
            $upstreamCode = $this->extractUpstreamResultCode($maskedResult);
            $status = $upstreamCode === '1001' ? 'success' : 'upstream_warning';

            $this->recordAudit(
                requestId: $requestId,
                request: $request,
                maskedMukellefNo: $maskedMukellefNo,
                captchaOk: $captchaOk,
                status: $status,
                startedAt: $startedAt,
                upstreamResultCode: $upstreamCode
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Borç sorgulama tamamlandı.',
                'request_id' => $requestId,
                'data' => $maskedResult,
            ]);
        } catch (RuntimeException $e) {
            $this->recordAudit(
                requestId: $requestId,
                request: $request,
                maskedMukellefNo: $maskedMukellefNo,
                captchaOk: $captchaOk,
                status: 'upstream_unavailable',
                startedAt: $startedAt
            );

            return $this->errorResponse(
                message: 'Borç sorgu servisine şu anda ulaşılamıyor. Lütfen daha sonra tekrar deneyin.',
                errorCode: 'UPSTREAM_UNAVAILABLE',
                statusCode: 503,
                requestId: $requestId
            );
        } catch (Throwable $e) {
            $this->recordAudit(
                requestId: $requestId,
                request: $request,
                maskedMukellefNo: $maskedMukellefNo,
                captchaOk: $captchaOk,
                status: 'unknown_error',
                startedAt: $startedAt
            );

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
    ): JsonResponse {
        return response()->json(array_merge([
            'status' => 'error',
            'message' => $message,
            'error_code' => $errorCode,
            'request_id' => $requestId,
        ], $extra), $statusCode);
    }

    protected function maskSensitiveData(mixed $value, ?string $key = null): mixed
    {
        if (is_array($value)) {
            $masked = [];
            foreach ($value as $childKey => $childValue) {
                $masked[$childKey] = $this->maskSensitiveData($childValue, is_string($childKey) ? $childKey : null);
            }

            return $masked;
        }

        if (is_object($value)) {
            return $this->maskSensitiveData((array) $value, $key);
        }

        if (! is_string($value)) {
            return $value;
        }

        $normalizedKey = strtolower((string) $key);
        if (in_array($normalizedKey, ['mukellefno', 'mukellef_no', 'tckn', 'vkn', 'sicilno', 'aboneno'], true)) {
            return $this->maskIdentifier($value);
        }

        if (in_array($normalizedKey, ['adisoyadiunvani', 'adsoyadunvan', 'full_name'], true)) {
            return $this->maskFullName($value);
        }

        return $value;
    }

    protected function maskIdentifier(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        $length = mb_strlen($trimmed);
        if ($length <= 2) {
            return str_repeat('*', $length);
        }

        if ($length <= 4) {
            return mb_substr($trimmed, 0, 1).str_repeat('*', $length - 1);
        }

        return mb_substr($trimmed, 0, 2).str_repeat('*', $length - 4).mb_substr($trimmed, -2);
    }

    protected function maskFullName(string $value): string
    {
        $parts = preg_split('/\s+/', trim($value)) ?: [];
        if ($parts === []) {
            return '';
        }

        $maskedParts = array_map(function (string $part): string {
            if ($part === '') {
                return '';
            }
            if (mb_strlen($part) <= 1) {
                return '*';
            }

            return mb_substr($part, 0, 1).str_repeat('*', max(1, mb_strlen($part) - 1));
        }, $parts);

        return implode(' ', $maskedParts);
    }

    protected function extractUpstreamResultCode(mixed $payload): ?string
    {
        if (! is_array($payload)) {
            return null;
        }

        if (isset($payload['sonucKodu'])) {
            return (string) $payload['sonucKodu'];
        }

        foreach ($payload as $value) {
            $candidate = $this->extractUpstreamResultCode($value);
            if ($candidate !== null) {
                return $candidate;
            }
        }

        return null;
    }

    protected function recordAudit(
        string $requestId,
        Request $request,
        string $maskedMukellefNo,
        bool $captchaOk,
        string $status,
        float $startedAt,
        ?string $upstreamResultCode = null,
        bool $rateLimited = false
    ): void {
        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
        $payload = [
            'request_id' => $requestId,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'mukellef_tipi' => (string) $request->input('mukellef_tipi', ''),
            'masked_mukellef_no' => $maskedMukellefNo,
            'captcha_ok' => $captchaOk,
            'rate_limited' => $rateLimited,
            'upstream_result_code' => $upstreamResultCode,
            'status' => $status,
            'duration_ms' => $durationMs,
        ];

        try {
            DebtQueryAudit::query()->create($payload);
        } catch (Throwable) {
            // Audit tablosu erişilemiyorsa akışı kesme.
        }

        Log::channel('debt_query_audit')->info('Debt query attempt', $payload);
    }
}
