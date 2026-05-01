<?php

namespace Tests\Feature;

use App\Models\Obituary;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ObituaryApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('obituaries', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->date('death_date');
            $table->time('prayer_time');
            $table->string('mosque');
            $table->string('burial_place_type')->default('city_cemetery');
            $table->string('burial_place_other')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('obituaries');

        parent::tearDown();
    }

    public function test_it_returns_only_active_obituaries_in_expected_order(): void
    {
        Obituary::query()->create([
            'full_name' => 'Pasif Kayit',
            'death_date' => '2026-04-01',
            'prayer_time' => '13:00:00',
            'mosque' => 'Hizirbey Camii',
            'burial_place_type' => 'city_cemetery',
            'is_active' => false,
            'sort_order' => 0,
        ]);

        Obituary::query()->create([
            'full_name' => 'Birinci Siradaki',
            'death_date' => '2026-04-03',
            'prayer_time' => '14:15:00',
            'mosque' => 'Karacaibrahim Camii',
            'burial_place_type' => 'other',
            'burial_place_other' => 'Koy Mezarligi',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Obituary::query()->create([
            'full_name' => 'Ikinci Siradaki',
            'death_date' => '2026-04-05',
            'prayer_time' => '15:30:00',
            'mosque' => 'Hizirbey Camii',
            'burial_place_type' => 'city_cemetery',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $response = $this->getJson('/api/obituaries');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.full_name', 'Birinci Siradaki');
        $response->assertJsonPath('data.1.full_name', 'Ikinci Siradaki');
        $response->assertJsonPath('data.0.prayer_time', '14:15');
        $response->assertJsonPath('data.0.burial_place_type', 'other');
        $response->assertJsonPath('data.0.burial_place_other', 'Koy Mezarligi');
        $response->assertJsonPath('data.0.burial_place', 'Koy Mezarligi');
        $response->assertJsonPath('data.1.burial_place', 'Şehir Mezarlığı');
    }
}
