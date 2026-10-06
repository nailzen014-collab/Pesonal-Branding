<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi form admin proyek.
 *
 * Konsep: Form Request memisahkan aturan validasi dari controller,
 * sehingga controller hanyalogika bisnis, dan aturan mudah diuji ulang.
 */
class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Sumber proyek default-nya manual, checkbox dianggap false bila tidak dicentang.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'source' => $this->input('source', 'manual'),
            'is_featured' => $this->boolean('is_featured'),
            'is_featured_manual' => true,
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('projects', 'slug')],
            'summary' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'source' => ['required', Rule::in(['manual', 'github'])],
            'is_featured' => ['nullable', 'boolean'],
            'is_featured_manual' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['integer', Rule::exists('technologies', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul proyek wajib diisi.',
            'summary.required' => 'Ringkasan proyek wajib diisi.',
            'repo_url.url' => 'Tautan repository harus berupa URL yang valid.',
            'demo_url.url' => 'Tautan demo harus berupa URL yang valid.',
            'thumbnail.max' => 'Ukuran thumbnail maksimal 2 MB.',
            'slug.unique' => 'Slug tersebut sudah dipakai proyek lain.',
        ];
    }
}
