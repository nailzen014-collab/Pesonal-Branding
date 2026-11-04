<?php

namespace App\Services;

/**
 * Impor artikel blog dari file Markdown di repository GitHub.
 *
 * File dicari di folder `blog` pada setiap repository publik milik user.
 * Format front matter sama persis dengan impor lokal, karena keduanya
 * memakai MarkdownPostImporter. Artikel yang filenya sudah tidak ada tidak
 * dihapus agar artikel buatan admin tidak ikut hilang.
 */
class GithubBlogImporter
{
    public function __construct(
        private readonly GithubService $github,
        private readonly MarkdownPostImporter $markdown,
    ) {}

    /**
     * @return array{repositories: int, files: int, created: int, updated: int, skipped: int}
     */
    public function sync(bool $fresh = false): array
    {
        $repositories = collect($this->github->repositories($fresh))
            ->reject(fn (array $repo) => (bool) ($repo['is_private'] ?? false))
            ->values()
            ->all();

        $summary = [
            'repositories' => count($repositories),
            'files' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        foreach ($repositories as $repo) {
            foreach ($this->github->blogMarkdownFiles($repo, $fresh) as $path) {
                $contents = $this->github->rawFile($repo, $path);

                if ($contents === null) {
                    $summary['skipped']++;

                    continue;
                }

                $summary['files']++;

                match ($this->markdown->upsert($contents)) {
                    'created' => $summary['created']++,
                    'updated' => $summary['updated']++,
                    default => $summary['skipped']++,
                };
            }
        }

        return $summary;
    }
}
