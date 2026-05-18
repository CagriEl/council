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
    Schema::create('council_decisions', function (Blueprint $table) {
        $table->id();
        $table->integer('year'); // Yıl (Örn: 2025)
        $table->string('month'); // Ay (Örn: Ocak)
        $table->string('title'); // Oturum Adı (Örn: Ocak Ayı Olağan Meclis Toplantısı)
        $table->date('meeting_date'); // Sıralama için tam tarih
        
        // PDF Dosyaları
        $table->string('agenda_file')->nullable(); // Gündem
        $table->string('decision_file')->nullable(); // Karar Özetleri
        $table->string('commission_file')->nullable(); // Komisyon Raporları
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_decisions');
    }
};
