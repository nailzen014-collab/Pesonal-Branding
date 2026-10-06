<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Baca nilai pengaturan situs di dalam Blade.
     *
     * Contoh: {{ setting('site_name', 'Abbad Nailun Nabhan') }}
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}
