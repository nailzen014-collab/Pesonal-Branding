<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Pengaturan situs (FR-20).
 *
 * Nilai diambil dari config/site.php supaya mudah diubah per lingkungan,
 * lalu ditulis ke tabel settings supaya bisa diedit dari panel admin.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setMany(config('site'));
    }
}
