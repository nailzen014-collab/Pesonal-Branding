<?php

namespace App\Http\Requests;

use App\Models\Skill;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSkillRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', Rule::in(array_keys(Skill::CATEGORIES))],
            'level' => ['required', 'integer', 'min:0', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama skill wajib diisi.',
            'category.in' => 'Kategori skill tidak valid.',
            'level.required' => 'Level skill wajib diisi.',
        ];
    }
}
