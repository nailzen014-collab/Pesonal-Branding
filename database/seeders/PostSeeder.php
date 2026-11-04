<?php

namespace Database\Seeders;

use App\Services\MarkdownPostImporter;
use Illuminate\Database\Seeder;

/**
 * Artikel blog diambil dari file Markdown di resources/content/blog.
 *
 * Dengan begitu isi blog ikut tersimpan di repository dan bisa dipulihkan
 * lewat `php artisan migrate --seed` atau `php artisan blog:sync`.
 */
class PostSeeder extends Seeder
{
    public function run(): void
    {
        app(MarkdownPostImporter::class)->sync();
    }
}
