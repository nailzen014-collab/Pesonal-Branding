<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Form akun admin: ganti email dan password.
 */
class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_akun_menampilkan_email_admin_saat_ini(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);

        $this->actingAs($admin)
            ->get(route('admin.account.edit'))
            ->assertOk()
            ->assertSee('admin@example.com')
            ->assertSee(route('admin.account.email.update'), false)
            ->assertSee(route('admin.account.password.update'), false);
    }

    public function test_tamu_tidak_bisa_membuka_halaman_akun(): void
    {
        $this->get(route('admin.account.edit'))->assertRedirect(route('login'));
    }

    public function test_tamu_tidak_bisa_mengubah_email_atau_password(): void
    {
        $admin = User::factory()->create();

        $this->put(route('admin.account.email.update'), [
            'email' => 'baru@example.com',
            'current_password' => 'password',
        ])->assertRedirect(route('login'));

        $this->put(route('admin.account.password.update'), [
            'current_password' => 'password',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertRedirect(route('login'));

        $this->assertSame($admin->email, $admin->fresh()->email);
        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
    }

    public function test_admin_bisa_mengganti_email(): void
    {
        $admin = User::factory()->create(['email' => 'lama@example.com']);

        $this->actingAs($admin)
            ->put(route('admin.account.email.update'), [
                'email' => 'baru@example.com',
                'current_password' => 'password',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.account.edit'));

        $this->assertSame('baru@example.com', $admin->fresh()->email);
    }

    public function test_ganti_email_menghapus_status_verifikasi(): void
    {
        $admin = User::factory()->create(['email' => 'lama@example.com']);

        $this->actingAs($admin)
            ->put(route('admin.account.email.update'), [
                'email' => 'baru@example.com',
                'current_password' => 'password',
            ]);

        $this->assertNull($admin->fresh()->email_verified_at);
    }

    public function test_ganti_email_ditolak_bila_password_saat_ini_salah(): void
    {
        $admin = User::factory()->create(['email' => 'lama@example.com']);

        $this->actingAs($admin)
            ->put(route('admin.account.email.update'), [
                'email' => 'baru@example.com',
                'current_password' => 'password-salah',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertSame('lama@example.com', $admin->fresh()->email);
    }

    public function test_ganti_email_ditolak_bila_email_dipakai_akun_lain(): void
    {
        User::factory()->create(['email' => 'dipakai@example.com']);
        $admin = User::factory()->create(['email' => 'lama@example.com']);

        $this->actingAs($admin)
            ->put(route('admin.account.email.update'), [
                'email' => 'dipakai@example.com',
                'current_password' => 'password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertSame('lama@example.com', $admin->fresh()->email);
    }

    public function test_ganti_email_format_tidak_valid_ditolak(): void
    {
        $admin = User::factory()->create(['email' => 'lama@example.com']);

        $this->actingAs($admin)
            ->put(route('admin.account.email.update'), [
                'email' => 'bukan-email',
                'current_password' => 'password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertSame('lama@example.com', $admin->fresh()->email);
    }

    public function test_admin_bisa_mengganti_password(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.account.password.update'), [
                'current_password' => 'password',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.account.edit'));

        $this->assertTrue(Hash::check('password-baru', $admin->fresh()->password));
    }

    public function test_ganti_password_ditolak_bila_password_saat_ini_salah(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.account.password.update'), [
                'current_password' => 'password-salah',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
    }

    public function test_ganti_password_ditolak_bila_konfirmasi_tidak_cocok(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.account.password.update'), [
                'current_password' => 'password',
                'password' => 'password-baru',
                'password_confirmation' => 'beda-password',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
    }
}
