<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 'council_decisions' tablosu üzerinde işlem yapıyoruz
        Schema::table('council_decisions', function (Blueprint $table) {
            
            // 1. Hata veren 'date' sütunu yoksa ekle
            if (!Schema::hasColumn('council_decisions', 'date')) {
                $table->date('date')->nullable(); 
            }
            
            // 2. Yıl filtresi için 'year' sütunu
            if (!Schema::hasColumn('council_decisions', 'year')) {
                $table->integer('year')->nullable();
            }

            // 3. Dosya yolları (Gündem, Karar, Komisyon)
            if (!Schema::hasColumn('council_decisions', 'agenda_file')) {
                $table->string('agenda_file')->nullable();
            }
            if (!Schema::hasColumn('council_decisions', 'decision_file')) {
                $table->string('decision_file')->nullable();
            }
            if (!Schema::hasColumn('council_decisions', 'commission_file')) {
                $table->string('commission_file')->nullable();
            }
            
            // 4. Başlık sütunu
            if (!Schema::hasColumn('council_decisions', 'title')) {
                $table->string('title')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('council_decisions', function (Blueprint $table) {
            // Geri alma işlemi gerekirse burası doldurulabilir
        });
    }
};