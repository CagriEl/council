<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('council_members', function (Blueprint $table) {
            // is_active sütunu yoksa ekle
            if (!Schema::hasColumn('council_members', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            
            // party sütunu yoksa ekle (Tasarımda kullanılıyor)
            if (!Schema::hasColumn('council_members', 'party')) {
                $table->string('party')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('council_members', function (Blueprint $table) {
            if (Schema::hasColumn('council_members', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('council_members', 'party')) {
                $table->dropColumn('party');
            }
        });
    }
};