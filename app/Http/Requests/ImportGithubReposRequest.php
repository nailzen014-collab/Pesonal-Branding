<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi pemilihan repo saat sinkronisasi GitHub (FR-14).
 */
class ImportGithubReposRequest extends FormRequest
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
            'repos' => ['required', 'array', 'min:1'],
            'repos.*' => ['integer', 'min:1'],
            'publish' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'repos.required' => 'Pilih minimal satu repository untuk diimpor.',
        ];
    }
}
