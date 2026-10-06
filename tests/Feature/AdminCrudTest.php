<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CRUD panel admin (FR-12 sampai FR-14).
 */
class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_semua_halaman_admin_dapat_dirender(): void
    {
        $project = Project::factory()->create();
        $skill = Skill::factory()->create();
        $experience = Experience::create([
            'type' => 'education',
            'title' => 'Siswa SMK',
            'start_date' => '2022-07-01',
        ]);
        $certificate = Certificate::create(['title' => 'Sertifikat Uji']);

        $pages = [
            route('admin.dashboard'),
            route('admin.projects.index'),
            route('admin.projects.create'),
            route('admin.projects.edit', $project),
            route('admin.skills.index'),
            route('admin.skills.create'),
            route('admin.skills.edit', $skill),
            route('admin.experiences.index'),
            route('admin.experiences.create'),
            route('admin.experiences.edit', $experience),
            route('admin.certificates.index'),
            route('admin.certificates.create'),
            route('admin.certificates.edit', $certificate),
            route('admin.github.index'),
            route('admin.settings.edit'),
        ];

        foreach ($pages as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    public function test_admin_bisa_membuat_dan_menghapus_proyek(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), [
                'title' => 'Proyek Baru',
                'summary' => 'Ringkasan proyek uji.',
                'description' => 'Deskripsi proyek uji.',
                'category' => 'Laravel',
                'is_published' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', ['title' => 'Proyek Baru']);

        $project = Project::where('title', 'Proyek Baru')->firstOrFail();

        $this->actingAs($this->admin)
            ->delete(route('admin.projects.destroy', $project))
            ->assertRedirect();

        $this->assertDatabaseMissing('projects', ['title' => 'Proyek Baru']);
    }

    public function test_admin_bisa_menambah_skill(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => 'Vue',
                'category' => 'frontend',
                'level' => 60,
                'sort_order' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('skills', ['name' => 'Vue']);
    }

    public function test_admin_bisa_memperbarui_pengaturan_situs(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), [
                'site_name' => 'Nama Baru',
                'role' => 'Developer',
                'tagline' => 'Tagline baru.',
                'bio' => 'Bio baru.',
                'education_level' => 'SMK',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'site_name', 'value' => 'Nama Baru']);
    }

    public function test_formulir_proyek_menolak_data_tidak_valid(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), [])
            ->assertSessionHasErrors(['title', 'summary']);
    }
}
