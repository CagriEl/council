<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Veritabanında eski yanlış başkan adı kalmışsa tek seferlik düzeltme.
     * Güncel kayıt zaten "Derya BULUT" ise dokunulmaz.
     */
    public function up(): void
    {
        if (! Schema::hasTable('mayors')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        $like = $driver === 'pgsql' ? 'ilike' : 'like';

        DB::table('mayors')
            ->where(function ($q) use ($like) {
                $q->where('name', $like, '%Mehmet%Siyam%')
                    ->orWhere('name', $like, '%KESIMOĞLU%')
                    ->orWhere('name', $like, '%Kesimoğlu%');
            })
            ->update([
                'name' => 'Derya BULUT',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Geri alınmaz; önceki değer saklanmadı.
    }
};
