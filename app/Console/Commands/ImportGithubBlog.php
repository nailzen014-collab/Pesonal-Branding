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

        $this->components->info(sprintf(
            '%d artikel baru, %d diperbarui, %d dilewati dari %d file di %d repository.',
            $summary['created'],
            $summary['updated'],
            $summary['skipped'],
            $summary['files'],
            $summary['repositories'],
        ));

        return self::SUCCESS;
    }
}
