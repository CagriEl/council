<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->index(); // web, android, ios, kiosk
            $table->string('source')->nullable(); // iletisim-sayfasi, footer, baskan-formu
            $table->json('payload'); // Ad, Soyad, Mesaj her şey burada
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_messages');
    }
};