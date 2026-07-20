<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('platform', 20)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('push_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('content_type', 32);
            $table->unsignedBigInteger('content_id');
            $table->string('title');
            $table->text('body');
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notification_logs');
        Schema::dropIfExists('push_tokens');
    }
};
