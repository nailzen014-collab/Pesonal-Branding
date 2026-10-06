<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Services\GithubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Unggulan di beranda mengikuti status admin di field is_featured.
 */
class FeaturedProjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_mark_projects_as_featured_manually(): void
    {
        Project::factory()->create(['title' => 'Alpha', 'source' => 'github', 'is_featured' => true, 'is_published' => true]);
        Project::factory()->create(['title' => 'Beta', 'source' => 'github', 'is_featured' => false, 'is_published' => true]);

        $this->assertSame(['Alpha'], Project::featured()->pluck('title')->all());
    }

    public function test_home_page_only_shows_admin_featured_projects(): void
    {
        Project::factory()->create([
            'title' => 'Alpha',
            'source' => 'github',
            'is_featured' => true,
            'is_published' => true,
            'sort_order' => 2,
        ]);

        Project::factory()->create([
            'title' => 'Beta',
            'source' => 'github',
            'is_featured' => true,
            'is_published' => true,
            'sort_order' => 1,
        ]);

        Project::factory()->create([
            'title' => 'Gamma',
            'source' => 'github',
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 3,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['Beta', 'Alpha'], false)
            ->assertDontSee('Gamma', false);
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
            $mock->shouldReceive('pinnedRepositories')->with(true)->andReturn($pinned);
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
