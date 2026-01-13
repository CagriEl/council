<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            
            // Temel Bilgiler
            $table->string('title'); // Menü Adı
            $table->string('url')->nullable(); // Link
            $table->integer('order')->default(0); // Sıralama
            $table->enum('location', ['header', 'footer'])->default('header'); // Konum
            
            // İLİŞKİLER
            // 1. Sayfa İlişkisi (Otomatik doldurma için)
            $table->foreignId('page_id')->nullable()->constrained('pages')->onDelete('cascade');
            
            // 2. Alt Menü İlişkisi (Dropdown için)
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};