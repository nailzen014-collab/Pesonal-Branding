<?php

namespace App\Console\Commands;

use App\Services\GithubBlogImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Impor artikel blog dari file Markdown di repository GitHub.
 *
 * Hanya file .md di folder `blog` yang dibaca. File harus punya front matter
 * dengan `title` agar tidak sembarang catatan ikut masuk sebagai artikel.
 * Meliputi semua repo publik GITHUB_USERNAME serta repo di GITHUB_BLOG_REPOS.
 */
#[Signature('blog:import-github
    {--user= : Username GitHub, menimpa nilai GITHUB_USERNAME}
    {--fresh : Abaikan cache daftar repository dan daftar file}')]
#[Description('Impor artikel blog dari file Markdown di repository GitHub')]
class ImportGithubBlog extends Command
{
    public function handle(GithubBlogImporter $importer): int
    {
        if ($user = $this->option('user')) {
            config(['services.github.username' => $user]);
        }

        $summary = $importer->sync(fresh: (bool) $this->option('fresh'));

        foreach ($summary['details'] as $detail) {
            $this->components->twoColumnDetail(
                $detail['repo'] ?: '(tanpa nama)',
                $detail['error'] ?? sprintf(
                    '%d file, %d baru, %d diperbarui, %d dilewati, %d konflik',
                    $detail['files'],
                    $detail['created'],
                    $detail['updated'],
                    $detail['skipped'],
                    $detail['conflicts'],
                ),
            );
        }

        $this->newLine();
        $this->components->info(sprintf(
            '%d artikel baru, %d diperbarui, %d dilewati, %d konflik dari %d file di %d repository.',
            $summary['created'],
            $summary['updated'],
            $summary['skipped'],
            $summary['conflicts'],
            $summary['files'],
            $summary['repositories'],
        ));

        if ($summary['conflicts'] > 0) {
            $this->components->warn('Ada slug yang dipakai lebih dari satu repo; yang pertama dipakai dan sisanya dilewati. Ganti slug-nya lalu sinkronkan ulang.');
        }

        return self::SUCCESS;
    }
}
