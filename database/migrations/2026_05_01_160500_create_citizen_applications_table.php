<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizen_applications', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_no')->unique();
            $table->string('service_type');
            $table->string('full_name');
            $table->string('identity_no', 11)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('request_summary');
            $table->string('status')->default('received');
            $table->string('source')->nullable();
            $table->string('platform')->default('web');
            $table->string('ip_address', 45)->nullable();
            $table->string('assigned_unit')->nullable();
            $table->text('response_text')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['service_type', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizen_applications');
    }
};
