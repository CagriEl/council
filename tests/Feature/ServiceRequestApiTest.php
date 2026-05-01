<?php

namespace Tests\Feature;

use App\Models\ServiceRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ServiceRequestApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_no')->unique();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('open');
            $table->string('source')->nullable();
            $table->string('platform')->default('web');
            $table->string('ip_address', 45)->nullable();
            $table->string('assigned_unit')->nullable();
            $table->text('admin_note')->nullable();
            $table->text('response_text')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('service_requests');

        parent::tearDown();
    }

    public function test_it_creates_service_request_and_returns_tracking_number(): void
    {
        $response = $this->postJson('/api/service-requests', [
            'full_name' => 'Ali Yilmaz',
            'phone' => '05551234567',
            'email' => 'ali@example.com',
            'subject' => 'Sokak Aydinlatma',
            'description' => 'Mahallemizdeki lambalar yanmiyor.',
            'source' => 'talep-sikayet-sayfasi',
            'platform' => 'web',
        ]);

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('message', 'Talebiniz oluşturuldu.');
        $this->assertNotEmpty($response->json('tracking_no'));

        $this->assertDatabaseCount('service_requests', 1);
        $this->assertDatabaseHas('service_requests', [
            'full_name' => 'Ali Yilmaz',
            'status' => 'open',
        ]);
    }

    public function test_it_tracks_request_with_tracking_number(): void
    {
        $request = ServiceRequest::query()->create([
            'full_name' => 'Ayse Demir',
            'subject' => 'Yol Bakimi',
            'description' => 'Cadde kaldiriminda sorun var.',
            'status' => 'in_review',
            'response_text' => 'Saha ekibine iletildi.',
        ]);

        $response = $this->getJson('/api/service-requests/'.$request->tracking_no);

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.tracking_no', $request->tracking_no);
        $response->assertJsonPath('data.current_status', 'in_review');
        $response->assertJsonPath('data.response', 'Saha ekibine iletildi.');
    }
}
