<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Services\GithubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Unggulan di beranda diatur otomatis dari sinkron GitHub, kecuali admin
 * sudah memilih sendiri lewat form proyek.
 */
class FeaturedProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_marks_top_repositories_as_featured_automatically(): void
    {
        $this->fakeGithub([
            $this->repo(1, 'alpha', stars: 5),
            $this->repo(2, 'beta', stars: 9),
            $this->repo(3, 'gamma', stars: 1),
            $this->repo(4, 'delta', stars: 7),
        ]);

        Artisan::call('portfolio:import-github', ['--publish' => true, '--featured' => 2]);

        $this->assertSame(
            ['Delta', 'Beta'],
            Project::featured()->orderByDesc('title')->pluck('title')->all(),
        );
        $this->assertSame(2, Project::featured()->count());
    }

    public function test_import_follows_pinned_repositories_when_available(): void
    {
        $this->fakeGithub(
            [
                $this->repo(1, 'alpha', stars: 5),
                $this->repo(2, 'beta', stars: 9),
                $this->repo(3, 'gamma', stars: 1),
            ],
            pinned: ['gamma', 'alpha'],
        );

        Artisan::call('portfolio:import-github', ['--publish' => true]);

        // Bintang terbanyak seharusnya kalah oleh urutan pin.
        $this->assertEqualsCanonicalizing(
            ['Gamma', 'Alpha'],
            Project::featured()->pluck('title')->all(),
        );
        $this->assertSame(2, Project::featured()->count());
    }

    public function test_import_does_not_override_a_manual_featured_choice(): void
    {
        $this->fakeGithub([
            $this->repo(1, 'alpha', stars: 5),
            $this->repo(2, 'beta', stars: 9),
        ]);

        Artisan::call('portfolio:import-github', ['--publish' => true]);

        $manual = Project::where('title', 'Alpha')->firstOrFail();
        $manual->update(['is_featured' => true, 'is_featured_manual' => true]);

        Artisan::call('portfolio:import-github', ['--publish' => true, '--featured' => 1]);

        $fresh = $manual->fresh();
        $this->assertTrue($fresh->is_featured);
        $this->assertTrue($fresh->is_featured_manual);
        // Slot unggulan otomatis ditempati proyek lain, pilihan admin tetap utuh.
        $this->assertSame(2, Project::featured()->count());
    }

    public function test_featured_scope_returns_only_featured_projects(): void
    {
        Project::factory()->featured()->count(2)->create(['is_published' => true]);
        Project::factory()->count(3)->create(['is_published' => true]);

        $this->assertSame(2, Project::published()->featured()->count());
    }

    /**
     * @param  array<int, array<string, mixed>>  $repositories
     * @param  array<int, string>  $pinned
     */
    private function fakeGithub(array $repositories, array $pinned = []): void
    {
        $this->mock(GithubService::class, function (MockInterface $mock) use ($repositories, $pinned): void {
            $mock->shouldReceive('repositories')->andReturn($repositories);
            $mock->shouldReceive('pinnedRepositories')->andReturn($pinned);
            $mock->shouldReceive('profileUrl')->andReturn('https://github.com/example');
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function repo(int $id, string $name, int $stars): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'full_name' => 'example/'.$name,
            'description' => 'Deskripsi '.$name,
            'language' => 'PHP',
            'url' => 'https://github.com/example/'.$name,
            'stars' => $stars,
            'forks' => 0,
            'topics' => [],
            'is_fork' => false,
            'is_private' => false,
            'updated_at' => '2026-10-01T00:00:00Z',
        ];
    }
}
