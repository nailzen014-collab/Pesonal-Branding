<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form Pengaturan situs (FR-20).
 */
class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', 'max:150'],
            'tagline' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'whatsapp_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'education_level' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'site_name.required' => 'Nama wajib diisi.',
            'role.required' => 'Role/jabatan wajib diisi.',
            'tagline.required' => 'Tagline wajib diisi.',
            'profile_photo.max' => 'Ukuran foto maksimal 2 MB.',
            'cv_file.mimes' => 'File CV harus berformat PDF.',
            'cv_file.max' => 'Ukuran CV maksimal 4 MB.',
        ];
    }
}
