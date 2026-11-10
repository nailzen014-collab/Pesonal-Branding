<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Impor artikel blog dari file Markdown di repository GitHub.
 */
class GithubBlogImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_impor_artikel_dari_folder_blog_repository_github(): void
    {
        $this->fakeGithub();

        $this->artisan('blog:import-github', ['--user' => 'blogger', '--fresh' => true])
            ->assertSuccessful();

        $this->assertDatabaseHas('posts', [
            'slug' => 'halo-github',
            'title' => 'Halo GitHub',
            'excerpt' => 'Dari GitHub.',
            'category' => 'Catatan',
        ]);

        $this->assertDatabaseMissing('posts', ['slug' => 'tanpa-judul']);
        $this->assertSame(1, Post::count());
    }

    public function test_artikel_dengan_slug_sama_diperbarui_bukan_diduplikasi(): void
    {
        Post::factory()->create(['slug' => 'halo-github', 'title' => 'Judul Lama']);

        $this->fakeGithub();

        $this->artisan('blog:import-github', ['--user' => 'blogger', '--fresh' => true])
            ->assertSuccessful();

        $this->assertSame(1, Post::count());
        $this->assertDatabaseHas('posts', [
            'slug' => 'halo-github',
            'title' => 'Halo GitHub',
        ]);
    }

    public function test_artikel_dari_beberapa_repository_lintas_akun_diimpor(): void
    {
        Http::fake($this->githubResponses() + $this->companionResponses('dari-teman', 'Dari Teman'));

        config(['services.github.blog_repos' => 'teman/catatan-bersama']);

        $this->artisan('blog:import-github', ['--user' => 'blogger', '--fresh' => true])
            ->assertSuccessful();

        $this->assertDatabaseHas('posts', ['slug' => 'halo-github']);
        $this->assertDatabaseHas('posts', ['slug' => 'dari-teman']);
        $this->assertSame(2, Post::count());
    }

    public function test_slug_sama_di_repo_lain_tidak_menimpa_artikel_pertama(): void
    {
        Http::fake($this->githubResponses() + $this->companionResponses('halo-github', 'Versi Teman'));

        config(['services.github.blog_repos' => 'teman/catatan-bersama']);

        $this->artisan('blog:import-github', ['--user' => 'blogger', '--fresh' => true])
            ->assertSuccessful();

        $this->assertSame(1, Post::count());
        $this->assertDatabaseHas('posts', [
            'slug' => 'halo-github',
            'title' => 'Halo GitHub',
        ]);
    }

    public function test_repo_tambahan_yang_tidak_ada_dilaporkan_sebagai_error(): void
    {
        config(['services.github.blog_repos' => 'teman/tidak-ada']);

        Http::fake([
            'https://api.github.com/users/*/repos*' => Http::response([]),
            'https://api.github.com/repos/teman/tidak-ada' => Http::response(['message' => 'Not Found'], 404),
        ]);

        $this->artisan('blog:import-github', ['--user' => 'blogger', '--fresh' => true])
            ->assertSuccessful();

        $this->assertSame(0, Post::count());
    }

    /**
     * @return array<string, mixed>
     */
    private function companionResponses(string $slug, string $title): array
    {
        return [
            'https://api.github.com/repos/teman/catatan-bersama' => Http::response([
                'id' => 99,
                'name' => 'catatan-bersama',
                'full_name' => 'teman/catatan-bersama',
                'description' => 'Kolaborasi',
                'language' => 'PHP',
                'html_url' => 'https://github.com/teman/catatan-bersama',
                'stargazers_count' => 0,
                'forks_count' => 0,
                'topics' => [],
                'fork' => false,
                'private' => false,
                'default_branch' => 'main',
                'updated_at' => '2026-01-01T00:00:00Z',
            ]),
            'https://api.github.com/repos/teman/catatan-bersama/git/trees/main*' => Http::response([
                'truncated' => false,
                'tree' => [
                    ['path' => 'blog/artikel-teman.md', 'type' => 'blob'],
                ],
            ]),
            'https://raw.githubusercontent.com/teman/catatan-bersama/main/blog/artikel-teman.md' => Http::response(<<<MD
            ---
            title: "{$title}"
            slug: {$slug}
            ---

            Isi artikel dari repo teman.
            MD),
        ];
    }

    private function fakeGithub(): void
    {
        Http::fake($this->githubResponses());
    }

    /**
     * @return array<string, mixed>
     */
    private function githubResponses(): array
    {
        return [
            'https://api.github.com/users/blogger/repos*' => Http::response([
                [
                    'id' => 42,
                    'name' => 'catatan',
                    'full_name' => 'blogger/catatan',
                    'description' => 'Catatan',
                    'language' => 'PHP',
                    'html_url' => 'https://github.com/blogger/catatan',
                    'stargazers_count' => 1,
                    'forks_count' => 0,
                    'topics' => [],
                    'fork' => false,
                    'private' => false,
                    'default_branch' => 'main',
                    'updated_at' => '2026-01-01T00:00:00Z',
                ],
            ]),
            'https://api.github.com/repos/blogger/catatan/git/trees/main*' => Http::response([
                'truncated' => false,
                'tree' => [
                    ['path' => 'README.md', 'type' => 'blob'],
                    ['path' => 'blog/halo-github.md', 'type' => 'blob'],
                    ['path' => 'blog/tanpa-judul.md', 'type' => 'blob'],
                    ['path' => 'src/index.php', 'type' => 'blob'],
                ],
            ]),
            'https://raw.githubusercontent.com/blogger/catatan/main/blog/halo-github.md' => Http::response(<<<'MD'
            ---
            title: "Halo GitHub"
            slug: halo-github
            excerpt: "Dari GitHub."
            category: Catatan
            ---

            Isi dari repository.
            MD),
            'https://raw.githubusercontent.com/blogger/catatan/main/blog/tanpa-judul.md' => Http::response('# Tanpa front matter'),
        ];
    }
}
