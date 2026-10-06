<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

/**
 * Contoh pesan masuk agar halaman kotak masuk admin tidak kosong (FR-15).
 */
class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'name' => 'Rina Kusuma',
                'email' => 'rina@example.com',
                'subject' => 'Tawaran magang backend',
                'body' => "Halo,\n\nSaya dari perusahaan XYZ. Kami tertarik menawarkan magang sebagai backend developer Laravel. Apakah Anda bisa dihubungi minggu ini?\n\nTerima kasih.",
                'read_at' => null,
            ],
            [
                'name' => 'Bagus Firmansyah',
                'email' => 'bagus@example.com',
                'subject' => 'Permintaan portofolio untuk kampus',
                'body' => "Selamat pagi,\n\nSaya dari bagian Ilmu Komputer. Bolehkah saya melihat portofolio lengkap Anda untuk keperluan tugas?\n\nSalam,\nBagus",
                'read_at' => now(),
            ],
        ];

        foreach ($messages as $message) {
            Message::updateOrCreate(
                ['email' => $message['email'], 'subject' => $message['subject']],
                $message,
            );
        }
    }
}
