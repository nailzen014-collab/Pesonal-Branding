<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
     */
    public static function allSettings(): array
    {
        return Cache::rememberForever('settings.all', fn () => static::query()->pluck('value', 'key')->all());
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
        Cache::forget('settings.all');
    }
}
