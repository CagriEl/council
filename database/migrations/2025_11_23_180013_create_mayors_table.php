<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mayors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Örn: Ahmet YILMAZ
            $table->string('title')->default('Belediye Başkanı');
            $table->string('image')->nullable(); // Fotoğraf yolu
            $table->text('quote')->nullable(); // Vurgulu söz
            $table->longText('content'); // Ana mesaj (HTML içerir)
            $table->text('biography')->nullable(); // Biyografi özeti
            $table->text('vision')->nullable(); // Vizyon özeti
            
            // Sosyal Medya
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('email')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mayors');
    }
};