<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Urutan seeding penting: pengaturan dipakai halaman publik, teknologi
 * direferensikan oleh proyek, lalu pesan contoh dibuat terakhir.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SettingSeeder::class,
            TechnologySeeder::class,
            SkillSeeder::class,
            ProjectSeeder::class,
            ExperienceSeeder::class,
            CertificateSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
