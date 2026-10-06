<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Seeder;

/**
 * Sertifikat dan pencapaian (FR-10).
 *
 * Catatan: kolom image diisi manual lewat panel admin karena file tidak
 * disertakan di dalam repository.
 */
class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'title' => 'Sertifikat Jwt Fresh Graduate',
                'issuer' => 'Sekolah',
                'issued_at' => '2024-06-30',
                'url' => null,
                'sort_order' => 1,
            ],
            [
                'title' => 'Sertifikat Partisipasi Webinar Laravel',
                'issuer' => 'Komunitas Laravel Indonesia',
                'issued_at' => '2024-08-15',
                'url' => null,
                'sort_order' => 2,
            ],
            [
                'title' => 'Sertifikat Hasil Kuesioner Perilaku',
                'issuer' => 'Psikotest',
                'issued_at' => '2025-01-20',
                'url' => null,
                'sort_order' => 3,
            ],
        ];

        foreach ($rows as $row) {
            Certificate::updateOrCreate(['title' => $row['title']], $row);
        }
    }
}
