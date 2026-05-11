<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $guard = (string) config('auth.defaults.guard', 'web');

        $role = Role::query()->firstOrCreate(
            ['name' => config('filament-shield.super_admin.name', 'super_admin'), 'guard_name' => $guard],
        );

        $email = (string) env('FILAMENT_ADMIN_EMAIL', 'admin@kirklareli.local');
        $password = (string) env('FILAMENT_ADMIN_PASSWORD', 'password');

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => (string) env('FILAMENT_ADMIN_NAME', 'Panel Yöneticisi'),
                'password' => $password,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        $this->command?->info(sprintf(
            'Filament yöneticisi: %s — Giriş: /admin (parola .env içinde FILAMENT_ADMIN_PASSWORD, varsayılan: password)',
            $email
        ));
    }
}
