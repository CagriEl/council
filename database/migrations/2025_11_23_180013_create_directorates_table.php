<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directorates', function (Blueprint $table) {
            $table->id();
            
            // Bağlı olduğu başkan yardımcısı
            $table->foreignId('vice_president_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('name'); // Müdürlük Adı
            $table->string('slug')->unique(); // <--- EKSİK OLAN BU SATIRDI, EKLENDİ
            
            // Müdür Bilgileri
            $table->string('manager_name')->nullable();
            $table->string('manager_title')->default('Müdür V.');
            $table->string('manager_image')->nullable();
            
            // İletişim
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            // İçerik
            $table->longText('description')->nullable(); 
    
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directorates');
    }
};