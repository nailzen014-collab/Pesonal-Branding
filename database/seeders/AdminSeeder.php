<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun admin tunggal.
 *
 * Publikasi tidak menyediakan pendaftaran (FR-17), jadi akun ini dibuat
 * lewat seeder. Password diambil dari .env agar tidak hardcode di repo.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ],
        );
    }
}
