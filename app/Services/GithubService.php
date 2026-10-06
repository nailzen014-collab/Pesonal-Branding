<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Mengambil daftar repository dari GitHub REST API (FR-14).
 *
 * Konsep:
 * - Controller tetap tipis: semua komunikasi ke GitHub ditulis di service.
 * - Hasil API disimpan di cache 1 jam supaya tidak kena rate limit.
 * - Bila API gagal, method mengembalikan array kosong dan halaman memakai
 *   data yang sudah tersimpan di database (lihat NFR-08).
 */
class GithubService
{
    public function username(): string
    {
        return (string) config('services.github.username', 'nailzen014-collab');
    }

    /**
     * Daftar repository, sudah di-cache.
     *
     * @param  bool  $fresh  abaikan cache dan ambil ulang dari API
     * @param  string  $sort  'updated' (terbaru) atau 'stars' (populer)
     * @param  bool  $includeForks  sertakan repository hasil fork
     * @return array<int, array<string, mixed>>
     */
    public function repositories(bool $fresh = false, string $sort = 'updated', bool $includeForks = false): array
    {
        $minutes = (int) config('services.github.cache_minutes', 60);
        $sort = $sort === 'stars' ? 'stars' : 'updated';
        $key = $this->cacheKey().'.'.$sort.($includeForks ? '.forks' : '');

        if ($fresh) {
            Cache::forget($key);
        }

        return Cache::remember($key, now()->addMinutes($minutes), function () use ($sort, $includeForks): array {
            try {
                $response = Http::withHeaders($this->headers())
                    ->timeout(10)
                    ->get($this->apiUrl());

                if ($response->failed()) {
                    Log::warning('GitHub API gagal', ['status' => $response->status()]);

                    return [];
                }

                $repositories = collect($response->json() ?? [])
                    ->when(! $includeForks, fn ($repos) => $repos->reject(
                        fn (array $repo) => (bool) ($repo['fork'] ?? false)
                    ))
                    ->map(fn (array $repo) => $this->normalize($repo));

                // Halaman admin menampilkan yang paling populer, daftar proyek
                // publik mengikuti urutan pembaruan terakhir dari GitHub.
                return ($sort === 'stars' ? $repositories->sortByDesc('stars') : $repositories)
                    ->values()
                    ->all();
            } catch (Throwable $exception) {
                Log::warning('GitHub API tidak bisa dihubungi: '.$exception->getMessage());

                return [];
            }
        });
    }

    /**
     * URL profil GitHub pemilik.
     */
    public function profileUrl(): string
    {
        return 'https://github.com/'.$this->username();
    }

    /**
     * Daftar nama repository yang dipinned di profil GitHub, sesuai urutan.
     *
     * API resmi tidak menyediakan daftar pinned untuk user biasa tanpa token
     * GraphQL, jadi halaman profil dibaca langsung. Hasil disimpan 15 menit
     * supaya tidak membebani github.com.
     *
     * @return array<int, string> nama repository, mis. ['schoolhub', 'json']
     */
    public function pinnedRepositories(bool $fresh = false): array
    {
        $key = 'github.pinned.'.$this->username();

        if ($fresh) {
            Cache::forget($key);
        }

        return Cache::remember($key, now()->addMinutes(15), function (): array {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => config('app.name'),
                    'Accept' => 'text/html',
                ])->timeout(10)->get($this->profileUrl());

                if ($response->failed()) {
                    Log::warning('Profil GitHub gagal dibaca', ['status' => $response->status()]);

                    return [];
                }

                return $this->parsePinned($response->body());
            } catch (Throwable $exception) {
                Log::warning('Profil GitHub tidak bisa dihubungi: '.$exception->getMessage());

                return [];
            }
        });
    }

    /**
     * Ambil nama repository dari blok "Pinned" pada HTML profil GitHub.
     *
     * Blok itu dirender ulang oleh JavaScript bila permintaan pertama gagal,
     * jadi parsing sengaja dibuat longgar: hasil kosong bukan error.
     *
     * @return array<int, string>
     */
    private function parsePinned(string $html): array
    {
        if (! preg_match('/js-pinned-items-reorder-list(.*?)<\/ol>/s', $html, $block)) {
            return [];
        }

        preg_match_all('/<li\b.*?<\/li>/s', $block[1], $items);

        $names = [];

        foreach ($items[0] as $item) {
            if (preg_match('#href="/'.preg_quote($this->username(), '/').'/([^"?#/]+)"#', $item, $match)) {
                $names[] = urldecode($match[1]);
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * Penyederhanaan data API menjadi field yang dipakai website.
     *
     * @param  array<string, mixed>  $repo
     * @return array<string, mixed>
     */
    private function normalize(array $repo): array
    {
        return [
            'id' => (int) $repo['id'],
            'name' => $repo['name'],
            'full_name' => $repo['full_name'],
            'description' => $repo['description'] ?: 'Belum ada deskripsi di repository ini.',
            'language' => $repo['language'] ?: null,
            'url' => $repo['html_url'],
            'stars' => (int) ($repo['stargazers_count'] ?? 0),
            'forks' => (int) ($repo['forks_count'] ?? 0),
            'topics' => $repo['topics'] ?? [],
            'is_fork' => (bool) ($repo['fork'] ?? false),
            'is_private' => (bool) ($repo['private'] ?? false),
            'updated_at' => $repo['updated_at'] ?? null,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        $headers = [
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
        ];

        if ($token = config('services.github.token')) {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }

    private function apiUrl(): string
    {
        return 'https://api.github.com/users/'.$this->username().'/repos?per_page=100&sort=updated';
    }

    private function cacheKey(): string
    {
        return 'github.repositories.'.$this->username();
    }
}
