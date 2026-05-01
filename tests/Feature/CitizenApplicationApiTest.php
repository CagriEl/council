<?php

namespace Tests\Feature;

use App\Models\CitizenApplication;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CitizenApplicationApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('citizen_applications', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_no')->unique();
            $table->string('service_type');
            $table->string('full_name');
            $table->string('identity_no', 11)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('request_summary');
            $table->string('status')->default('received');
            $table->string('source')->nullable();
            $table->string('platform')->default('web');
            $table->string('ip_address', 45)->nullable();
            $table->string('assigned_unit')->nullable();
            $table->text('response_text')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('citizen_applications');

        parent::tearDown();
    }

    public function test_it_creates_citizen_application_and_returns_tracking_number(): void
    {
        $response = $this->postJson('/api/citizen-applications', [
            'service_type' => 'ruhsat',
            'full_name' => 'Mehmet Kaya',
            'identity_no' => '12345678901',
            'phone' => '05550001122',
            'email' => 'mehmet@example.com',
            'address' => 'Kırklareli Merkez',
            'request_summary' => 'İşyeri açma ruhsatı başvurusu hakkında bilgi talep ediyorum.',
        ]);

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $this->assertNotEmpty($response->json('tracking_no'));

        $this->assertDatabaseCount('citizen_applications', 1);
        $this->assertDatabaseHas('citizen_applications', [
            'full_name' => 'Mehmet Kaya',
            'service_type' => 'ruhsat',
            'status' => 'received',
        ]);
    }

    public function test_it_tracks_citizen_application_by_tracking_number(): void
    {
        $application = CitizenApplication::query()->create([
            'service_type' => 'sosyal_destek',
            'full_name' => 'Fatma Demir',
            'request_summary' => 'Sosyal destek başvurusu.',
            'status' => 'in_process',
            'response_text' => 'İnceleme devam ediyor.',
        ]);

        $response = $this->getJson('/api/citizen-applications/'.$application->tracking_no);

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.tracking_no', $application->tracking_no);
        $response->assertJsonPath('data.service_type', 'sosyal_destek');
        $response->assertJsonPath('data.status', 'in_process');
        $response->assertJsonPath('data.response', 'İnceleme devam ediyor.');
    }
}
