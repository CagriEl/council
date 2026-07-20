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
                $table->string('title')->default('Meclis Üyesi')->nullable();
                $table->string('party')->nullable();
                $table->string('image_path')->nullable();
                $table->integer('order')->default(0);
                $table->foreignId('political_party_id')->nullable()->constrained()->nullOnDelete();
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

            if (! Schema::hasColumn('council_members', 'political_party_id')) {
                $table->foreignId('political_party_id')->nullable()->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('council_members', 'image_path')) {
                $table->string('image_path')->nullable();
            }

            if (! Schema::hasColumn('council_members', 'order')) {
                $table->integer('order')->default(0);
            }

            if (! Schema::hasColumn('council_members', 'title')) {
                $table->string('title')->default('Meclis Üyesi')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_members');
    }
};
