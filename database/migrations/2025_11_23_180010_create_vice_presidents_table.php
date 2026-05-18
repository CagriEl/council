<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vice_presidents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
$table->string('title')->default('Belediye Başkan Yardımcısı');
$table->string('image_path')->nullable();
$table->integer('order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vice_presidents');
    }
};
