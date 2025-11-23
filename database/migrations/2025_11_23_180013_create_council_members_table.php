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
        Schema::create('council_members', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('political_party_id')->nullable()->constrained()->nullOnDelete();
$table->string('name');
$table->string('title')->default('Meclis Üyesi');
$table->string('image_path')->nullable();
$table->integer('order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_members');
    }
};
