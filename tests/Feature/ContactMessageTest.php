<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pesan dari form kontak (FR-09) dan akses panel admin (FR-16).
 */
class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengunjung_terautentikasi_bisa_mengirim_pesan(): void
    {
        $visitor = Visitor::factory()->create();

        $this->actingAs($visitor, 'visitor')
            ->post(route('kontak.store'), [
                'subject' => 'Halo',
                'body' => 'Ini pesan uji coba.',
            ])
            ->assertRedirect(route('kontak'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('messages', [
            'name' => $visitor->name,
            'email' => $visitor->email,
        ]);
    }

    public function test_tamu_tidak_bisa_mengirim_pesan(): void
    {
        $this->post(route('kontak.store'), [
            'subject' => 'Halo',
            'body' => 'Ini pesan uji coba.',
        ])
            ->assertRedirect(route('kontak'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_pesan_tidak_valid_ditolak(): void
    {
        $visitor = Visitor::factory()->create();

        $this->actingAs($visitor, 'visitor')
            ->post(route('kontak.store'), [])
            ->assertSessionHasErrors(['subject', 'body']);

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_panel_admin_hanya_bisa_diakses_setelah_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.projects.index'))->assertRedirect(route('login'));
    }

    public function test_admin_login_dan_melihat_pesan(): void
    {
        $user = User::factory()->create();
        $message = Message::factory()->create(['read_at' => null]);

        $this->actingAs($user)
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSee($message->subject);

        $this->actingAs($user)
            ->put(route('admin.messages.read', $message))
            ->assertRedirect();

        $this->assertNotNull($message->fresh()->read_at);
    }
}
