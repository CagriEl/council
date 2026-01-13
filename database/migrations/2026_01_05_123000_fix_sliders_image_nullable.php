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
        Schema::table('sliders', function (Blueprint $table) {
            // image_path sütununu NULL (boş) değer alabilir hale getiriyoruz.
            $table->string('image_path')->nullable()->change();
            
            // Eğer video_path daha önce eklenmediyse diye kontrol (Garanti olsun)
            if (!Schema::hasColumn('sliders', 'video_path')) {
                $table->string('video_path')->nullable()->after('image_path');
            } else {
                // Varsa da nullable olduğundan emin olalım
                $table->string('video_path')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma işlemi (gerekirse)
        Schema::table('sliders', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change();
        });
    }
};