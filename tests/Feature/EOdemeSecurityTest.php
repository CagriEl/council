<?php

namespace Tests\Feature;

use App\Services\EOdemeService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class EOdemeSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('debt_query_audits', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 64)->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('mukellef_tipi', 10)->nullable()->index();
            $table->string('masked_mukellef_no', 64)->nullable()->index();
            $table->boolean('captcha_ok')->default(false)->index();
            $table->boolean('rate_limited')->default(false)->index();
            $table->string('upstream_result_code', 20)->nullable()->index();
            $table->string('status', 50)->index();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('debt_query_audits');

        parent::tearDown();
    }

    public function test_it_returns_standard_error_when_turnstile_validation_fails(): void
    {
        config()->set('services.turnstile.enabled', true);
        config()->set('services.turnstile.secret_key', 'secret');
        config()->set('services.turnstile.verify_url', 'https://turnstile.test/verify');

        Http::fake([
            'https://turnstile.test/verify' => Http::response(['success' => false], 200),
        ]);

        $this->mock(EOdemeService::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('borcSorgula');
        });

        $response = $this->postJson('/api/eodeme/borc-sorgula', [
            'mukellef_tipi' => 'TCKN',
            'mukellef_no' => '12345678901',
            'cf_turnstile_response' => 'invalid-token',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('status', 'error');
        $response->assertJsonPath('error_code', 'CAPTCHA_FAILED');
        $response->assertJsonStructure(['request_id']);
    }

    public function test_it_masks_sensitive_fields_and_records_audit_on_success(): void
    {
        config()->set('services.turnstile.enabled', true);
        config()->set('services.turnstile.secret_key', 'secret');
        config()->set('services.turnstile.verify_url', 'https://turnstile.test/verify');

        Http::fake([
            'https://turnstile.test/verify' => Http::response(['success' => true], 200),
        ]);

        $this->mock(EOdemeService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('borcSorgula')->once()->andReturn([
                'sonucKodu' => 1001,
                'adiSoyadiUnvani' => 'Ahmet Yilmaz',
                'mukellefNo' => '12345678901',
                'tahakkukListesi' => [],
            ]);
        });

        $response = $this->postJson('/api/eodeme/borc-sorgula', [
            'mukellef_tipi' => 'TCKN',
            'mukellef_no' => '12345678901',
            'cf_turnstile_response' => 'valid-token',
        ]);

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.mukellefNo', '12*******01');
        $response->assertJsonPath('data.adiSoyadiUnvani', 'A**** Y*****');

        $this->assertDatabaseHas('debt_query_audits', [
            'status' => 'success',
            'captcha_ok' => 1,
            'upstream_result_code' => '1001',
        ]);
    }

    public function test_it_applies_rate_limit_with_standard_error_payload(): void
    {
        config()->set('services.turnstile.enabled', false);
        config()->set('services.e_odeme.rate_limit_ip_attempts', 1);
        config()->set('services.e_odeme.rate_limit_id_attempts', 1);
        config()->set('services.e_odeme.rate_limit_decay_minutes', 10);

        $this->mock(EOdemeService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('borcSorgula')->andReturn([
                'sonucKodu' => 1001,
                'tahakkukListesi' => [],
            ]);
        });

        $payload = [
            'mukellef_tipi' => 'TCKN',
            'mukellef_no' => '12345678901',
        ];

        $firstResponse = $this->postJson('/api/eodeme/borc-sorgula', $payload);
        $firstResponse->assertOk();

        $secondResponse = $this->postJson('/api/eodeme/borc-sorgula', $payload);
        $secondResponse->assertStatus(429);
        $secondResponse->assertJsonPath('status', 'error');
        $secondResponse->assertJsonPath('error_code', 'RATE_LIMITED');
        $secondResponse->assertJsonStructure(['request_id']);

        $this->assertDatabaseHas('debt_query_audits', [
            'status' => 'rate_limited',
            'rate_limited' => 1,
        ]);
    }

    public function test_it_hides_internal_exception_message_from_clients(): void
    {
        config()->set('services.turnstile.enabled', false);

        $this->mock(EOdemeService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('borcSorgula')->once()->andThrow(new RuntimeException('Sensitive host detail'));
        });

        $response = $this->postJson('/api/eodeme/borc-sorgula', [
            'mukellef_tipi' => 'TCKN',
            'mukellef_no' => '12345678901',
        ]);

        $response->assertStatus(503);
        $response->assertJsonPath('status', 'error');
        $response->assertJsonPath('error_code', 'UPSTREAM_UNAVAILABLE');
        $this->assertStringNotContainsString('Sensitive host detail', (string) $response->getContent());
    }
}
