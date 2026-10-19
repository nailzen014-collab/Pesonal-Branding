<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Once;

/**
 * Pengaturan situs disimpan sebagai pasangan key-value (FR-20).
 *
 * Nilai diambil sekali lalu disimpan di cache supaya tidak query database
 * pada setiap halaman yang membutuhkan (mis. nama, tagline, link sosial).
 */
class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected static function booted(): void
    {
        // Setiap perubahan pengaturan langsung membuang cache.
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    /**
     * Semua pengaturan dalam bentuk array ['key' => 'value'].
     *
     * Hasilnya diingat sekali per request memakai `once()`. Tanpa ini,
     * setiap pemanggilan `setting()` di Blade tetap menembak cache store
     * (satu query database bila CACHE_STORE=database), sehingga halaman
     * dengan puluhan `setting()` menghasilkan puluhan query duplikat.
     */
    public static function allSettings(): array
    {
        return once(fn (): array => Cache::rememberForever(
            'settings.all',
            fn (): array => static::query()->pluck('value', 'key')->all(),
        ));
    }

    /**
     * Ambil satu nilai pengaturan, atau nilai bawaan bila belum diatur.
     *
     * String kosong dianggap belum diatur, supaya link sosial yang belum
     * diisi admin tidak menghasilkan href="" (yang menunjuk ke halaman ini).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::allSettings()[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    /**
     * Simpan banyak nilai sekaligus (dipakai form Pengaturan).
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        static::flushCache();
    }

    public static function flushCache(): void
    {
        // Buang juga hasil memoization `once()` di request yang sama agar
        // perubahan pengaturan langsung terlihat (mis. setelah form disimpan).
        Once::flush();

        Cache::forget('settings.all');
    }
}
