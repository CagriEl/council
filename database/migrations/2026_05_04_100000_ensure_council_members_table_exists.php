<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Eski 2025_11_23_180014 dosyası yalnızca ALTER yapıyordu; tablo hiç oluşmadıysa
 * veya migration "Ran" görünüp tablo yoksa bu migration tabloyu oluşturur.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('council_members')) {
            return;
        }

        Schema::create('council_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('party')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('order')->default(0);
            $table->foreignId('political_party_id')->nullable()->constrained('political_parties')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Önceki migration ile aynı tablo; silme — veri kaybını önlemek için boş.
    }
};
