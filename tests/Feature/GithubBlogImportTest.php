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

    private function fakeGithub(): void
    {
        Http::fake([
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
        ]);
    }
}
