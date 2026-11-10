<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Sinkronisasi artikel blog dari file Markdown ke database.
 *
 * File Markdown adalah sumber konten yang disimpan di repository, sedangkan
 * tabel `posts` dipakai untuk pencarian, filter, dan pagination. Setiap file
 * diawali front matter sederhana (satu pasangan `kunci: nilai` per baris).
 */
class MarkdownPostImporter
{
    /**
     * Impor seluruh file .md di direktori, lalu perbarui artikel dengan slug
     * yang sama. Artikel yang filenya sudah tidak ada sengaja tidak dihapus
     * agar artikel buatan admin tidak ikut terhapus.
     *
     * @return array{created: int, updated: int, skipped: int}
     */
    public function sync(?string $directory = null): array
    {
        $directory ??= resource_path('content/blog');

        if (! File::isDirectory($directory)) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $files = Collection::make(File::files($directory))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->sortBy(fn ($file) => $file->getFilename());

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $result = $this->upsert($file->getContents());

            if ($result === 'skipped') {
                Log::warning('Artikel blog dilewati karena front matter tidak valid', [
                    'file' => $file->getFilename(),
                ]);
            }

            match ($result) {
                'created' => $created++,
                'updated' => $updated++,
                default => $skipped++,
            };
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped];
    }

    /**
     * Simpan satu artikel dari isi Markdown.
     *
     * Dipakai bersama oleh impor file lokal dan impor dari GitHub supaya
     * format front matter hanya punya satu sumber kebenaran.
     *
     * @return string 'created', 'updated', atau 'skipped'
     */
    public function upsert(string $contents): string
    {
        $data = $this->parse($contents);

        if ($data === null) {
            return 'skipped';
        }

        $exists = Post::where('slug', $data['slug'])->exists();

        Post::updateOrCreate(['slug' => $data['slug']], $data);

        return $exists ? 'updated' : 'created';
    }

    /**
     * Baca satu artikel tanpa menyimpan. Dipakai importer GitHub untuk
     * memeriksa slug sebelum menulis.
     *
     * @return array<string, mixed>|null
     */
    public function parse(string $contents): ?array
    {
        $contents = str_replace(["\r\n", "\r"], "\n", $contents);

        if (! str_starts_with($contents, "---\n")) {
            return null;
        }

        $end = strpos($contents, "\n---", 4);

        if ($end === false) {
            return null;
        }

        $meta = $this->parseFrontMatter(substr($contents, 4, $end - 4));
        $body = trim(substr($contents, $end + 4));

        if (blank($meta['title'] ?? null)) {
            return null;
        }

        return [
            'title' => $meta['title'],
            'slug' => $this->slug($meta['slug'] ?? null, $meta['title']),
            'excerpt' => $this->string($meta['excerpt'] ?? null),
            'body' => $body === '' ? null : $body,
            'category' => $this->string($meta['category'] ?? null),
            'thumbnail' => $this->string($meta['thumbnail'] ?? null),
            'is_published' => filter_var($meta['is_published'] ?? true, FILTER_VALIDATE_BOOL),
            'published_at' => $this->date($meta['published_at'] ?? null),
            'sort_order' => (int) ($meta['sort_order'] ?? 0),
            'meta_description' => $this->string($meta['meta_description'] ?? null),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function parseFrontMatter(string $header): array
    {
        $meta = [];

        foreach (explode("\n", $header) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $meta[trim($key)] = $this->string(trim($value));
        }

        return $meta;
    }

    private function string(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || in_array(strtolower($value), ['null', '~'], true)) {
            return null;
        }

        if (preg_match('/^(["\'])(.*)\1$/s', $value, $matches) === 1) {
            $value = stripcslashes($matches[2]);
        }

        return $value;
    }

    private function slug(?string $slug, string $title): string
    {
        return Str::slug($slug ?: $title) ?: 'artikel';
    }

    private function date(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }
}
