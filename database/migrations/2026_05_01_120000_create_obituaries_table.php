<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            $table->index(['is_active', 'sort_order', 'death_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obituaries');
    }
};
