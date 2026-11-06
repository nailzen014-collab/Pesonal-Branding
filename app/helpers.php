<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

if (! function_exists('public_storage_url')) {
    /**
     * URL publik untuk file di disk "public".
     *
     * Path lama yang menunjuk ke aset statis (images/...) tetap didukung.
     */
    function public_storage_url(?string $path, ?string $default = null): ?string
    {
        if (blank($path)) {
            return $default;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}

if (! function_exists('public_file_response')) {
    /**
     * Sajikan file dari folder public tanpa membuka celah path traversal.
     */
    function public_file_response(string $directory, string $path, string $cacheControl = 'public, max-age=604800'): BinaryFileResponse
    {
        $root = realpath(public_path($directory));
        $file = realpath(public_path($directory.'/'.$path));

        abort_if(
            $root === false || $file === false || ! str_starts_with($file, $root.DIRECTORY_SEPARATOR),
            404,
        );

        return response()->file($file, ['Cache-Control' => $cacheControl]);
    }
}
