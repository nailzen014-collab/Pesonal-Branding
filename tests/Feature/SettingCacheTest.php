<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

/**
 * Memoization pengaturan agar `setting()` tidak menembak cache berulang.
 */
class SettingCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengaturan_hanya_diambil_sekali_per_request(): void
    {
        Cache::shouldReceive('rememberForever')
            ->once()
            ->with('settings.all', Mockery::type('Closure'))
            ->andReturn(['site_name' => 'Nama Situs']);

        $this->assertSame('Nama Situs', Setting::get('site_name'));
        $this->assertSame('Nama Situs', Setting::get('site_name'));
        $this->assertSame('Nama Situs', setting('site_name'));
    }

    public function test_flush_cache_membuat_pengaturan_dibaca_ulang(): void
    {
        Setting::create(['key' => 'site_name', 'value' => 'Lama']);

        $this->assertSame('Lama', Setting::get('site_name'));

        // Update langsung tanpa event, lalu pastikan flushCache membuang
        // memoization `once()` sekaligus cache persist.
        Setting::query()->where('key', 'site_name')->update(['value' => 'Baru']);
        Setting::flushCache();

        $this->assertSame('Baru', Setting::get('site_name'));
    }
}
