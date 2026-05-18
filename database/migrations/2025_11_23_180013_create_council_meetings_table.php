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
        Schema::create('council_meetings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('year');
$table->string('month');
$table->string('title'); // "Ekim Ayı Olağan Toplantısı"
$table->date('meeting_date');
$table->json('documents')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_meetings');
    }
};
