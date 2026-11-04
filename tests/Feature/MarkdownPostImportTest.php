<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Services\MarkdownPostImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Impor artikel blog dari file Markdown (fitur konten berbasis repository).
 */
class MarkdownPostImportTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = storage_path('framework/testing/blog-'.Str::random(8));
        File::ensureDirectoryExists($this->directory);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    public function test_importer_membuat_lalu_memperbarui_artikel_dari_markdown(): void
    {
        $this->writePost('artikel-uji.md', 'Artikel Uji', 'Ringkasan awal.');

        $result = app(MarkdownPostImporter::class)->sync($this->directory);

        $this->assertSame(['created' => 1, 'updated' => 0, 'skipped' => 0], $result);

        $post = Post::where('slug', 'artikel-uji')->firstOrFail();
        $this->assertSame('Artikel Uji', $post->title);
        $this->assertTrue($post->is_published);
        $this->assertSame(3, $post->sort_order);
        $this->assertSame('Laravel', $post->category);
        $this->assertStringContainsString('<strong>Markdown</strong>', $post->body_html);

        $this->writePost('artikel-uji.md', 'Artikel Uji Diperbarui', 'Ringkasan baru.');

        $result = app(MarkdownPostImporter::class)->sync($this->directory);

        $this->assertSame(['created' => 0, 'updated' => 1, 'skipped' => 0], $result);
        $this->assertSame(1, Post::count());
        $this->assertDatabaseHas('posts', [
            'slug' => 'artikel-uji',
            'title' => 'Artikel Uji Diperbarui',
            'excerpt' => 'Ringkasan baru.',
        ]);
    }

    public function test_file_tanpa_front_matter_dilewati(): void
    {
        File::put($this->directory.'/tanpa-front-matter.md', '# Judul saja');

        $result = app(MarkdownPostImporter::class)->sync($this->directory);

        $this->assertSame(['created' => 0, 'updated' => 0, 'skipped' => 1], $result);
        $this->assertSame(0, Post::count());
    }

    public function test_artikel_dari_markdown_tampil_di_halaman_blog(): void
    {
        $this->writePost('artikel-uji.md', 'Artikel Uji', 'Ringkasan awal.');

        app(MarkdownPostImporter::class)->sync($this->directory);

        $this->get(route('blog', ['q' => 'Artikel Uji']))
            ->assertOk()
            ->assertSee('Artikel Uji');

        $this->get(route('blog.show', 'artikel-uji'))
            ->assertOk()
            ->assertSee('Isi artikel uji dengan <strong>Markdown</strong>.', false);
    }

    private function writePost(string $filename, string $title, string $excerpt): void
    {
        File::put($this->directory.'/'.$filename, <<<MD
        ---
        title: "{$title}"
        slug: artikel-uji
        excerpt: "{$excerpt}"
        category: Laravel
        published_at: 2026-01-02 08:00
        sort_order: 3
        is_published: true
        meta_description: "Deskripsi uji."
        ---

        ## Pendahuluan

        Isi artikel uji dengan **Markdown**.
        MD);
    }
}
