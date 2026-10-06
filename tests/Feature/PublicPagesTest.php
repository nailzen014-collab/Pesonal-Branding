<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman publik harus bisa diakses tamu dan hanya menampilkan proyek
 * yang dipublikasikan (FR-07).
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_dapat_diakses_tamu(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Portofolio', false);
    }

    public function test_halaman_statis_dapat_diakses(): void
    {
        foreach (['tentang', 'skills', 'sertifikat', 'kontak', 'portofolio'] as $name) {
            $this->get(route($name))->assertOk();
        }
    }

    public function test_proyek_ditampilkan_hanya_jika_published(): void
    {
        $published = Project::factory()->create(['title' => 'Proyek Terbuka', 'is_published' => true]);
        Project::factory()->create(['title' => 'Proyek Rahasia', 'is_published' => false]);

        $this->get(route('portofolio'))
            ->assertOk()
            ->assertSee('Proyek Terbuka')
            ->assertDontSee('Proyek Rahasia');

        $this->get(route('portofolio.show', $published->slug))->assertOk();
    }

    public function test_proyek_draft_mengembalikan_404(): void
    {
        $draft = Project::factory()->create(['is_published' => false]);

        $this->get(route('portofolio.show', $draft->slug))->assertNotFound();
    }

    public function test_proyek_bisa_difilter_berdasarkan_kategori_dan_teknologi(): void
    {
        $laravel = Project::factory()->create(['category' => 'Laravel']);
        $laravel->technologies()->attach(Technology::factory()->create(['name' => 'Laravel']));

        Project::factory()->create(['category' => 'PHP']);

        $this->get(route('portofolio', ['category' => 'Laravel']))->assertOk();
        $this->get(route('portofolio', ['technology' => 'Laravel']))->assertOk();
        $this->get(route('portofolio', ['q' => 'tidak-ada-hasil']))->assertOk();
    }

    public function test_portofolio_hanya_menampilkan_sembilan_proyek_pertama(): void
    {
        Project::factory()->count(25)->create(['is_published' => true]);

        $this->get(route('portofolio'))
            ->assertOk()
            ->assertSee('data-load-more', false)
            ->assertSee('data-next-page="2"', false)
            ->assertSee('Muat lebih banyak', false);
    }

    public function test_potongan_partial_mengembalikan_kartu_dan_halaman_berikutnya(): void
    {
        Project::factory()->count(25)->create(['is_published' => true]);

        $response = $this->get(route('portofolio', ['partial' => 1, 'page' => 2]));

        $response->assertOk()
            ->assertHeader('X-Next-Page', '3')
            ->assertSee('card card-hover', false)
            ->assertDontSee('<!DOCTYPE html>', false);
    }

    public function test_potongan_partial_menghentikan_muatan_di_halaman_terakhir(): void
    {
        Project::factory()->count(10)->create(['is_published' => true]);

        $response = $this->get(route('portofolio', ['partial' => 1, 'page' => 2]));

        $response->assertOk()
            ->assertSee('card card-hover', false)
            ->assertDontSee('Muat lebih banyak', false);
    }

    public function test_filter_tetap_berlaku_ke_halaman_berikutnya(): void
    {
        Project::factory()->count(24)->create(['is_published' => true, 'category' => 'PHP']);
        Project::factory()->create(['is_published' => true, 'category' => 'Laravel']);

        $this->get(route('portofolio', ['partial' => 1, 'page' => 2, 'category' => 'PHP']))
            ->assertOk()
            ->assertSee('data-query="category=PHP"', false)
            ->assertDontSee('Laravel', false);
    }

    public function test_halaman_kontak_menampilkan_formulir(): void
    {
        $this->get(route('kontak'))
            ->assertOk()
            ->assertSee('Kirim Pesan', false);
    }

    public function test_seo_sitemap_dan_robots_tersedia(): void
    {
        $this->get(route('sitemap'))->assertOk();
        $this->get(route('robots'))->assertOk();
    }
}
