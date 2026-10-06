<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;

/**
 * Seed dasar portofolio (FR-07).
 *
 * Hanya memuat proyek yang benar-benar ada di GitHub. Daftar lengkap
 * repository diambil otomatis lewat `php artisan portfolio:import-github`,
 * sehingga portofolio selalu mengikuti karya nyata, bukan data contoh.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'SchoolHub',
                'summary' => 'Sistem informasi sekolah: data siswa, guru, nilai, dan absensi.',
                'description' => "SchoolHub adalah sistem informasi sekolah untuk belajar framework Laravel.\n\nFitur utama:\n- Manajemen data siswa dan guru\n- Input dan rekap nilai\n- Absensi harian per kelas",
                'category' => 'Laravel',
                'repo_url' => 'https://github.com/nailzen014-collab/schoolhub',
                'sort_order' => 1,
                'technologies' => ['Laravel', 'Blade', 'PHP'],
            ],
            [
                'title' => 'Web Berita',
                'summary' => 'Aplikasi berita sederhana dengan kategori dan pencarian.',
                'description' => "Aplikasi berita sederhana yang dibuat untuk belajar PHP native.\n\nFitur utama:\n- Daftar berita per kategori\n- Pencarian berita\n- Halaman detail berita",
                'category' => 'PHP',
                'repo_url' => 'https://github.com/nailzen014-collab/web-berita',
                'sort_order' => 2,
                'technologies' => ['PHP', 'MySQL'],
            ],
            [
                'title' => 'Website Portofolio',
                'summary' => 'Portofolio pribadi dengan desain modern dan responsif.',
                'description' => "Website portofolio pribadi yang dibuat dengan CSS dan Tailwind CSS.\n\nFokus pada:\n- Tampilan responsif\n- Animasi yang halus\n- Struktur komponen yang rapi",
                'category' => 'Tailwind CSS',
                'repo_url' => 'https://github.com/nailzen014-collab/wesite_portofolio',
                'sort_order' => 3,
                'technologies' => ['HTML', 'CSS', 'Tailwind CSS'],
            ],
            [
                'title' => 'Data Klien',
                'summary' => 'Manajemen data klien untuk kebutuhan internal.',
                'description' => "Aplikasi manajemen data klien menggunakan Laravel Blade.\n\nFitur utama:\n- CRUD data klien\n- Pencarian dan filter\n- Ekspor data",
                'category' => 'Laravel',
                'repo_url' => 'https://github.com/nailzen014-collab/data_klien',
                'sort_order' => 4,
                'technologies' => ['Laravel', 'Blade', 'PHP'],
            ],
            [
                'title' => 'Webprogram Sertikom',
                'summary' => 'Web aplikasi untuk proyek sertifikasi kompetensi.',
                'description' => "Aplikasi web untuk proyek sertifikasi kompetensi.\n\nFitur utama:\n- Manajemen peserta\n- Soal dan penilaian\n- Rekap hasil",
                'category' => 'Laravel',
                'repo_url' => 'https://github.com/nailzen014-collab/Webprogram_sertikom',
                'sort_order' => 5,
                'technologies' => ['Laravel', 'Blade', 'PHP'],
            ],
        ];

        foreach ($projects as $data) {
            $technologies = $data['technologies'];
            unset($data['technologies']);

            $project = Project::updateOrCreate(
                ['slug' => Project::uniqueSlug($data['title'])],
                [
                    ...$data,
                    'source' => 'manual',
                    'is_published' => true,
                ],
            );

            $project->technologies()->sync(
                Technology::whereIn('name', $technologies)->pluck('id'),
            );
        }
    }
}
