<?php

namespace Tests\Feature\Auth;

use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

/**
 * Login pengunjung lewat Google (guard "visitor").
 */
class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengunjung_diarahkan_ke_google(): void
    {
        Socialite::shouldReceive('driver->redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        $this->get(route('visitor.google.redirect'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_callback_membuat_visitor_dan_login(): void
    {
        Socialite::shouldReceive('driver->user')
            ->once()
            ->andReturn($this->googleUser());

        $this->get(route('visitor.google.callback'))
            ->assertRedirect(route('kontak'))
            ->assertSessionHas('success');

        $visitor = Visitor::query()->where('google_id', 'google-123')->firstOrFail();

        $this->assertSame('Pengunjung Google', $visitor->name);
        $this->assertSame('pengunjung@gmail.com', $visitor->email);
        $this->assertAuthenticatedAs($visitor, 'visitor');
    }

    public function test_callback_login_ulang_tidak_menduplikasi_visitor(): void
    {
        $existing = Visitor::factory()->create([
            'google_id' => 'google-123',
            'email' => 'lama@gmail.com',
        ]);

        Socialite::shouldReceive('driver->user')->andReturn($this->googleUser());

        $this->get(route('visitor.google.callback'))->assertRedirect(route('kontak'));

        $this->assertDatabaseCount('visitors', 1);
        $this->assertSame('pengunjung@gmail.com', $existing->fresh()->email);
    }

    public function test_halaman_kontak_menampilkan_ajakan_login_google(): void
    {
        $this->get(route('kontak'))
            ->assertOk()
            ->assertSee('Masuk dengan akun Google')
            ->assertSee(route('visitor.google.redirect'), false);
    }

    public function test_halaman_kontak_menampilkan_form_setelah_login(): void
    {
        $visitor = Visitor::factory()->create(['name' => 'Pengunjung Google']);

        $this->actingAs($visitor, 'visitor')
            ->get(route('kontak'))
            ->assertOk()
            ->assertSee('Pengunjung Google')
            ->assertSee(route('visitor.google.logout'), false)
            ->assertDontSee('Masuk dengan akun Google');
    }

    public function test_pengunjung_bisa_keluar(): void
    {
        $visitor = Visitor::factory()->create();

        $this->actingAs($visitor, 'visitor')
            ->post(route('visitor.google.logout'))
            ->assertRedirect(route('kontak'));

        $this->assertGuest('visitor');
    }

    public function test_visitor_tidak_bisa_mengakses_panel_admin(): void
    {
        Socialite::shouldReceive('driver->user')->andReturn($this->googleUser());

        $this->get(route('visitor.google.callback'))->assertRedirect(route('kontak'));

        $this->assertAuthenticated('visitor');
        $this->assertGuest('web');

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_pesan_memakai_identitas_google_bukan_input_form(): void
    {
        $visitor = Visitor::factory()->create([
            'name' => 'Nama Google',
            'email' => 'google@gmail.com',
        ]);

        $this->actingAs($visitor, 'visitor')
            ->post(route('kontak.store'), [
                'name' => 'Nama Palsu',
                'email' => 'palsu@example.com',
                'subject' => 'Halo',
                'body' => 'Pesan uji coba dari pengunjung.',
            ])
            ->assertRedirect(route('kontak'));

        $this->assertDatabaseHas('messages', [
            'name' => 'Nama Google',
            'email' => 'google@gmail.com',
        ]);

        $this->assertDatabaseMissing('messages', [
            'email' => 'palsu@example.com',
        ]);
    }

    private function googleUser(): SocialiteUser
    {
        return (new SocialiteUser)->map([
            'id' => 'google-123',
            'name' => 'Pengunjung Google',
            'email' => 'pengunjung@gmail.com',
            'avatar' => 'https://lh3.googleusercontent.com/a/photo',
        ]);
    }
}
