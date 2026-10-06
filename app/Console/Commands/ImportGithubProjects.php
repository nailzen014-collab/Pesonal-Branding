<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Technology;
use App\Services\GithubService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Impor seluruh repository GitHub menjadi proyek portofolio (FR-14).
 *
 * Dipakai agar portofolio mencerminkan karya asli, bukan contoh data.
 * Repo yang sudah ada di database diperbarui, bukan diduplikasi.
 */
#[Signature('portfolio:import-github
    {--user= : Username GitHub, menimpa nilai GITHUB_USERNAME}
    {--dry-run : Tampilkan rencana tanpa menulis ke database}
    {--publish : Langsung terbitkan proyek hasil impor}
    {--include-forks : Sertakan repository yang merupakan fork}
    {--featured=6 : Jumlah proyek unggulan otomatis; mengikuti pinned GitHub bila ada}')]
#[Description('Impor semua repository GitHub ke daftar proyek portofolio')]
class ImportGithubProjects extends Command
{
    public function handle(GithubService $github): int
    {
        if ($user = $this->option('user')) {
            config(['services.github.username' => $user]);
        }

        $this->components->info('Mengambil repository dari '.$github->profileUrl());

        $repositories = $github->repositories(
            fresh: true,
            includeForks: (bool) $this->option('include-forks'),
        );

        if ($repositories === []) {
            $this->components->error(
                'Tidak ada repository yang diterima. Cek GITHUB_TOKEN dan koneksi internet.'
            );

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $created = 0;
        $updated = 0;

        foreach ($repositories as $index => $repo) {
            $existing = $this->findExisting($repo);
            $sortOrder = $index + 1;

            $attributes = [
                'title' => $this->title($repo['name']),
                'summary' => filled($repo['description'])
                    ? Str::limit($repo['description'], 200, '')
                    : 'Repository GitHub '.$this->title($repo['name']).'.',
                'description' => $this->description($repo),
                'category' => $repo['language'] ?: 'Lainnya',
                'repo_url' => $repo['url'],
                'source' => 'github',
                'is_published' => $this->option('publish') || $existing?->is_published === true,
                'sort_order' => $sortOrder,
            ];

            if ($dryRun) {
                $this->components->twoColumnDetail(
                    $existing ? 'Perbarui' : 'Tambah',
                    $repo['full_name'].($existing ? '' : ' (draft)'),
                );

                continue;
            }

            if ($existing) {
                // Judul, status, dan urutan tetap milik admin.
                $existing->fill($attributes + ['github_id' => $repo['id']])->save();
                $updated++;
            } else {
                Project::create($attributes + ['github_id' => $repo['id']]);
                $created++;
            }

            $this->attachTechnologies($repo);
        }

        if ($dryRun) {
            $this->newLine();
            $this->components->info('Mode dry-run: tidak ada data yang ditulis.');

            return self::SUCCESS;
        }

        $featured = $this->syncFeatured($repositories, $github);

        $this->newLine();
        $this->components->info(sprintf(
            '%d proyek baru, %d diperbarui dari total %d repository.',
            $created,
            $updated,
            count($repositories),
        ));

        if ($featured > 0) {
            $this->components->info($featured.' proyek ditandai unggulan (mengikuti pinned GitHub, fallback ke bintang dan pembaruan terakhir).');
        }

        if (! $this->option('publish') && $created > 0) {
            $this->components->warn(
                'Proyek baru disimpan sebagai draft. Terbitkan lewat panel admin atau ulangi dengan --publish.'
            );
        }

        return self::SUCCESS;
    }

    /**
     * Tandai unggulan secara otomatis untuk proyek yang belum disetel admin.
     *
     * Prioritasnya repository yang dipinned di profil GitHub, sesuai urutan
     * pin. Bila profil tidak punya pinned, jatuh ke bintang terbanyak lalu
     * pembaruan terakhir. Pilihan admin (is_featured_manual) tidak disentuh.
     *
     * @param  array<int, array<string, mixed>>  $repositories
     */
    private function syncFeatured(array $repositories, GithubService $github): int
    {
        $automatic = Project::where('is_featured_manual', false)->pluck('github_id', 'id');

        if ($automatic->isEmpty()) {
            return 0;
        }

        $ids = $this->featuredIds($repositories, $automatic, $github);

        $touched = Project::where('is_featured_manual', false)
            ->whereIn('id', $ids)
            ->update(['is_featured' => true]);

        Project::where('is_featured_manual', false)
            ->whereNotIn('id', $ids)
            ->update(['is_featured' => false]);

        return $touched;
    }

    /**
     * Pilih proyek yang layak ditandai unggulan.
     *
     * Prioritas pertama mengikuti repository yang dipinned di profil GitHub,
     * sesuai urutannya. Bila profil tidak punya pinned (atau gagal dibaca),
     * jatuh ke pilihan lama: bintang terbanyak lalu pembaruan terakhir.
     *
     * @param  array<int, array<string, mixed>>  $repositories
     * @param  Collection<int, int>  $automatic  id proyek => github_id
     * @return array<int, int>
     */
    private function featuredIds(array $repositories, $automatic, GithubService $github): array
    {
        $limit = max(0, (int) $this->option('featured'));

        if ($limit === 0) {
            return [];
        }

        $pinned = $github->pinnedRepositories();

        if ($pinned !== []) {
            $ids = collect($pinned)
                ->map(fn (string $name) => $this->findByName($repositories, $name))
                ->filter()
                ->map(fn (array $repo) => $automatic->search($repo['id']))
                ->filter()
                ->values()
                ->take($limit)
                ->all();

            if ($ids !== []) {
                return $ids;
            }
        }

        return collect($repositories)
            ->filter(fn (array $repo) => $automatic->contains($repo['id']))
            ->sortBy([['stars', 'desc'], ['updated_at', 'desc']])
            ->take($limit)
            ->map(fn (array $repo) => $automatic->search($repo['id']))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Cari data repository dari API berdasarkan nama (bukan full_name).
     *
     * @param  array<int, array<string, mixed>>  $repositories
     * @return array<string, mixed>|null
     */
    private function findByName(array $repositories, string $name): ?array
    {
        $needle = strtolower($name);

        foreach ($repositories as $repo) {
            if (strtolower((string) $repo['name']) === $needle) {
                return $repo;
            }
        }

        return null;
    }

    /**
     * Cari proyek yang sudah mewakili repository ini.
     *
     * Cocok lewat `github_id` dulu, lalu lewat `repo_url` supaya proyek yang
     * sebelumnya dibuat manual tidak terduplikasi saat impor pertama kali.
     *
     * @param  array<string, mixed>  $repo
     */
    private function findExisting(array $repo): ?Project
    {
        return Project::where('github_id', $repo['id'])
            ->orWhere('repo_url', $repo['url'])
            ->first();
    }

    /**
     * Judul proyek yang enak dibaca dari nama repository.
     *
     * `Str::headline()` merusak akronim (PHP -> "P H P"), jadi nama repo yang
     * sudah rapi dibiarkan apa adanya. Sisanya diubah seperti "web-berita"
     * menjadi "Web Berita".
     */
    private function title(string $name): string
    {
        if (preg_match('/[A-Z]/', $name) === 1) {
            return $name;
        }

        return Str::headline(str_replace('_', '-', $name));
    }

    /**
     * Nama teknologi siap tampil, rapi tanpa merusak akronim.
     */
    private function technologyName(string $name): string
    {
        if (Str::upper($name) === $name && mb_strlen($name) <= 8) {
            return $name;
        }

        return Str::headline($name);
    }

    /**
     * Deskripsi proyek yang bisa dibaca pengunjung, disusun dari data API.
     *
     * @param  array<string, mixed>  $repo
     */
    private function description(array $repo): string
    {
        $lines = [];

        if (filled($repo['description'])) {
            $lines[] = $repo['description'];
        }

        $lines[] = 'Repository publik di GitHub: '.$repo['full_name'].'.';

        $lines[] = 'Ringkasan: '
            .$repo['stars'].' bintang, '
            .$repo['forks'].' fork, '
            .($repo['language'] ?: 'bahasa tidak ditentukan').'.';

        if ($repo['topics'] !== []) {
            $lines[] = 'Topik: '.implode(', ', $repo['topics']).'.';
        }

        if (filled($repo['updated_at'])) {
            $lines[] = 'Pembaruan terakhir: '.substr($repo['updated_at'], 0, 10).'.';
        }

        return implode("\n\n", $lines);
    }

    /**
     * Hubungkan proyek dengan teknologi yang dikenal, membuat yang baru bila perlu.
     *
     * @param  array<string, mixed>  $repo
     */
    private function attachTechnologies(array $repo): void
    {
        $project = $this->findExisting($repo);

        if (! $project) {
            return;
        }

        $names = collect([$repo['language'], ...$repo['topics']])
            ->filter()
            ->map(fn (string $name) => $this->technologyName($name))
            ->filter(fn (string $name) => mb_strlen($name) <= 40)
            ->unique();

        if ($names->isEmpty()) {
            return;
        }

        $ids = $names->map(function (string $name) {
            // Pakai nama kanonik bila sudah ada, supaya "PHP" tidak membuat
            // teknologi kedua yang hanya berbeda kapitalisasi.
            return Technology::firstOrCreate(
                ['name' => $name],
                ['sort_order' => Technology::max('sort_order') + 1],
            )->id;
        });

        // Sinkron tanpa menghapus teknologi yang sudah dipilih admin.
        $project->technologies()->syncWithoutDetaching($ids->all());
    }
}
