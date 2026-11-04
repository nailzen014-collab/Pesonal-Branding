<?php

namespace App\Console\Commands;

use App\Services\MarkdownPostImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Sinkronkan artikel blog dari file Markdown ke database.
 *
 * Jalankan setelah menambah atau mengubah file di resources/content/blog.
 * File yang slug-nya sudah ada akan diperbarui, bukan diduplikasi.
 */
#[Signature('blog:sync')]
#[Description('Impor artikel blog dari resources/content/blog ke database')]
class SyncBlogPosts extends Command
{
    public function handle(MarkdownPostImporter $importer): int
    {
        $result = $importer->sync();

        $this->components->info(
            "Blog disinkronkan: {$result['created']} dibuat, {$result['updated']} diperbarui, {$result['skipped']} dilewati."
        );

        if ($result['created'] + $result['updated'] + $result['skipped'] === 0) {
            $this->components->warn('Tidak ada file Markdown di resources/content/blog.');
        }

        return self::SUCCESS;
    }
}
