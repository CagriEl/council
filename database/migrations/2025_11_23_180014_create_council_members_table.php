<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('council_members')) {
            Schema::create('council_members', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('title')->nullable();
                $table->string('party')->nullable();
                $table->string('image_path')->nullable();
                $table->integer('order')->default(0);
                $table->foreignId('political_party_id')->nullable()->constrained('political_parties')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            return;
        }

        Schema::table('council_members', function (Blueprint $table) {
            if (! Schema::hasColumn('council_members', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (! Schema::hasColumn('council_members', 'party')) {
                $table->string('party')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_members');
    }
};
