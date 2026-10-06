<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

/**
 * Timeline pendidikan, pengalaman, dan organisasi (FR-09).
 */
class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'type' => 'education',
                'title' => 'Siswa SMK Jurusan Rekayasa Perangkat Lunak',
                'organization' => 'SMK Negeri 1',
                'start_date' => '2022-07-01',
                'end_date' => null,
                'description' => 'Mempelajari dasar-dasar pemrograman, basis data, dan pengembangan web.',
                'sort_order' => 1,
            ],
            [
                'type' => 'experience',
                'title' => 'Belajar Mandiri Web Development',
                'organization' => 'Online',
                'start_date' => '2024-01-01',
                'end_date' => null,
                'description' => 'Latihan harian memakai Laravel, Blade, Tailwind CSS, dan Git.',
                'sort_order' => 2,
            ],
            [
                'type' => 'organization',
                'title' => 'Sekbid Seni dan Bahasa',
                'organization' => 'OSIS SMK Informatika Pesat',
                'start_date' => '2024-06-01',
                'end_date' => null,
                'description' => 'Menangani kegiatan seni dan bahasa di lingkungan OSIS sekolah.',
                'sort_order' => 3,
            ],
        ];

        foreach ($rows as $row) {
            Experience::updateOrCreate(['title' => $row['title']], $row);
        }
    }
}
