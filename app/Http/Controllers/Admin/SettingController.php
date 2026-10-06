<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Pengaturan situs: nama, role, tagline, bio, link sosial, foto, CV (FR-20).
 */
class SettingController extends Controller
{
    /**
     * Daftar key yang boleh diubah lewat form ini.
     *
     * Konsep: allow-list. Nilai di luar daftar ini tidak bisa ditulis dari
     * form, sehingga aman dari injection nilai asing.
     */
    private const EDITABLE_KEYS = [
        'site_name',
        'role',
        'tagline',
        'bio',
        'meta_description',
        'github_url',
        'instagram_url',
        'whatsapp_url',
        'linkedin_url',
        'education_level',
    ];

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => Setting::allSettings(),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $values = collect($request->validated())
            ->only(self::EDITABLE_KEYS)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->all();

        if ($request->hasFile('profile_photo')) {
            $old = Setting::get('profile_photo');

            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }

            $values['profile_photo'] = $request->file('profile_photo')->store('profile', 'public');
        }

        if ($request->hasFile('cv_file')) {
            $old = Setting::get('cv_file');

            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }

            $values['cv_file'] = $request->file('cv_file')->store('documents', 'public');
        }

        // Hapus file lama bila pengguna mencentang hapus.
        if ($request->boolean('remove_profile_photo')) {
            $old = Setting::get('profile_photo');

            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }

            $values['profile_photo'] = null;
        }

        if ($request->boolean('remove_cv_file')) {
            $old = Setting::get('cv_file');

            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }

            $values['cv_file'] = null;
        }

        Setting::setMany($values);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
