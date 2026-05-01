<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_no')->unique();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('open');
            $table->string('source')->nullable();
            $table->string('platform')->default('web');
            $table->string('ip_address', 45)->nullable();
            $table->string('assigned_unit')->nullable();
            $table->text('admin_note')->nullable();
            $table->text('response_text')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
