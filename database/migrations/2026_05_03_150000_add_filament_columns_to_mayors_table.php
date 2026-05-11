<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Eski mayors şeması (image, content, biography) ile Filament/model alanlarını hizalar.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mayors')) {
            return;
        }

        Schema::table('mayors', function (Blueprint $table) {
            if (! Schema::hasColumn('mayors', 'image_path')) {
                $table->string('image_path')->nullable()->after('title');
            }
            if (! Schema::hasColumn('mayors', 'description')) {
                $table->longText('description')->nullable()->after('image_path');
            }
            if (! Schema::hasColumn('mayors', 'message')) {
                $table->longText('message')->nullable()->after('description');
            }
        });

        if (Schema::hasColumn('mayors', 'image') && Schema::hasColumn('mayors', 'image_path')) {
            DB::table('mayors')->whereNull('image_path')->whereNotNull('image')->update([
                'image_path' => DB::raw('`image`'),
            ]);
        }

        if (Schema::hasColumn('mayors', 'biography') && Schema::hasColumn('mayors', 'description')) {
            DB::table('mayors')->whereNull('description')->whereNotNull('biography')->update([
                'description' => DB::raw('`biography`'),
            ]);
        }

        if (Schema::hasColumn('mayors', 'content') && Schema::hasColumn('mayors', 'message')) {
            DB::table('mayors')->whereNull('message')->whereNotNull('content')->update([
                'message' => DB::raw('`content`'),
            ]);
        }

        if (Schema::hasColumn('mayors', 'content') && Schema::hasColumn('mayors', 'description')) {
            DB::table('mayors')->whereNull('description')->whereNotNull('content')->update([
                'description' => DB::raw('`content`'),
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('mayors')) {
            return;
        }

        Schema::table('mayors', function (Blueprint $table) {
            if (Schema::hasColumn('mayors', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('mayors', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('mayors', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
