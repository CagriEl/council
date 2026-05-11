<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_query_audits', function (Blueprint $table) {
            $table->id();
            $table->string('request_id', 64)->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('mukellef_tipi', 10)->nullable()->index();
            $table->string('masked_mukellef_no', 64)->nullable()->index();
            $table->boolean('captcha_ok')->default(false)->index();
            $table->boolean('rate_limited')->default(false)->index();
            $table->string('upstream_result_code', 20)->nullable()->index();
            $table->string('status', 50)->index();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_query_audits');
    }
};
