<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('form_submissions', 'source')) {
                $table->string('source')->nullable()->after('id');
            }
            if (! Schema::hasColumn('form_submissions', 'platform')) {
                $table->string('platform')->default('web');
            }
            if (! Schema::hasColumn('form_submissions', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }
            if (! Schema::hasColumn('form_submissions', 'data')) {
                $table->json('data')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn(['source', 'platform', 'ip_address', 'data']);
        });
    }
};
