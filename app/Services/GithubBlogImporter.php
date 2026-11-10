<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Impor artikel blog dari file Markdown di repository GitHub.
 *
 * Sumber artikel ada dua: semua repository publik milik GITHUB_USERNAME,
 * ditambah repo lain dari GITHUB_BLOG_REPOS (`owner/repo`, dipisah koma).
 * Format front matter sama persis dengan impor lokal, karena keduanya
 * memakai MarkdownPostImporter. Artikel yang filenya sudah tidak ada tidak
 * dihapus agar artikel buatan admin tidak ikut hilang.
 *
 * Bila dua repo memakai slug yang sama, repo yang diproses lebih dulu
 * menang dan yang lain dicatat sebagai konflik supaya tidak menimpa
 * tulisan secara diam-diam.
 */
class GithubBlogImporter
{
    public function __construct(
        private readonly GithubService $github,
        private readonly MarkdownPostImporter $markdown,
    ) {}

    /**
     * @return array{repositories: int, files: int, created: int, updated: int, skipped: int, conflicts: int, errors: int, details: array<int, array<string, mixed>>}
     */
    public function sync(bool $fresh = false): array
    {
        $summary = [
            'repositories' => 0,
            'files' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'conflicts' => 0,
            'errors' => 0,
            'details' => [],
        ];

        // Slug yang sudah "dimiliki" di sesi ini; sumber pertama menang.
        $claimed = [];

        foreach ($this->sources($fresh) as [$repo, $error]) {
            $detail = [
                'repo' => $repo['full_name'] ?? '',
                'files' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'conflicts' => 0,
                'error' => $error,
                'conflict_slugs' => [],
                'skipped_files' => [],
            ];

            if ($error !== null) {
                $summary['errors']++;
                $summary['details'][] = $detail;

                continue;
            }

            $summary['repositories']++;

            foreach ($this->github->blogMarkdownFiles($repo, $fresh) as $path) {
                $contents = $this->github->rawFile($repo, $path);

                if ($contents === null) {
                    $detail['skipped']++;
                    $detail['skipped_files'][] = $path;
                    $summary['errors']++;

                    continue;
                }

                $detail['files']++;

                $data = $this->markdown->parse($contents);

                if ($data === null) {
                    $detail['skipped']++;
                    $detail['skipped_files'][] = $path;
                    Log::warning('Artikel blog dilewati karena front matter tidak valid', [
                        'repo' => $repo['full_name'],
                        'file' => $path,
                    ]);

                    continue;
                }

                $slug = $data['slug'];
                $slugOwner = $claimed[$slug] ?? null;

                if ($slugOwner !== null && $slugOwner !== $repo['full_name']) {
                    $detail['conflicts']++;
                    $detail['conflict_slugs'][] = $path;
                    Log::warning('Konflik slug artikel blog, tidak ditimpa', [
                        'slug' => $slug,
                        'repo' => $repo['full_name'],
                        'file' => $path,
                        'owner' => $slugOwner,
                    ]);

                    continue;
                }

                $claimed[$slug] = $repo['full_name'];

                match ($this->markdown->upsert($contents)) {
                    'created' => $detail['created']++,
                    'updated' => $detail['updated']++,
                    default => $detail['skipped']++,
                };
            }

            $summary['files'] += $detail['files'];
            $summary['created'] += $detail['created'];
            $summary['updated'] += $detail['updated'];
            $summary['skipped'] += $detail['skipped'];
            $summary['conflicts'] += $detail['conflicts'];
            $summary['details'][] = $detail;
        }

        return $summary;
    }

    /**
     * Daftar repo sumber beserta error bila repo tidak bisa dibaca.
     *
     * @return array<int, array{0: array<string, mixed>, 1: string|null}>
     */
    private function sources(bool $fresh): array
    {
        $sources = [];
        $seen = [];

        foreach (collect($this->github->repositories($fresh))->reject(fn (array $repo) => (bool) ($repo['is_private'] ?? false)) as $repo) {
            $seen[$repo['full_name']] = true;
            $sources[] = [$repo, null];
        }

        foreach ($this->github->blogRepositoryNames() as $fullName) {
            if (isset($seen[$fullName])) {
                continue;
            }

            $seen[$fullName] = true;
            $repo = $this->github->repository($fullName, $fresh);

            if ($repo === null) {
                $sources[] = [['full_name' => $fullName], 'Repository tidak ditemukan atau private.'];

                continue;
            }

            $sources[] = [$repo, null];
        }

        return $sources;
    }
}
