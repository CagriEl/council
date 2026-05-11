<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mayors') || ! Schema::hasColumn('mayors', 'content')) {
            return;
        }

        Schema::table('mayors', function (Blueprint $table) {
            $table->longText('content')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('mayors') || ! Schema::hasColumn('mayors', 'content')) {
            return;
        }

        Schema::table('mayors', function (Blueprint $table) {
            $table->longText('content')->nullable(false)->change();
        });
    }
};
