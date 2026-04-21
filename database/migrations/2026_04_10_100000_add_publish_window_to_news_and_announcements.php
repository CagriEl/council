<?php

use App\Models\Announcement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (! Schema::hasColumn('news', 'unpublished_at')) {
                $table->date('unpublished_at')->nullable()->after('published_at');
            }
        });

        Schema::table('announcements', function (Blueprint $table) {
            if (! Schema::hasColumn('announcements', 'published_at')) {
                $table->date('published_at')->nullable()->after('date');
            }
            if (! Schema::hasColumn('announcements', 'unpublished_at')) {
                $table->date('unpublished_at')->nullable()->after('published_at');
            }
        });

        foreach (Announcement::query()->whereNull('published_at')->cursor() as $row) {
            $row->published_at = $row->date;
            $row->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasColumn('news', 'unpublished_at')) {
                $table->dropColumn('unpublished_at');
            }
        });

        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'unpublished_at')) {
                $table->dropColumn('unpublished_at');
            }
            if (Schema::hasColumn('announcements', 'published_at')) {
                $table->dropColumn('published_at');
            }
        });
    }
};
