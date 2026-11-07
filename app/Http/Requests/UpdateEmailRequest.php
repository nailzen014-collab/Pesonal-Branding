<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi form ganti email akun admin.
 *
 * current_password dipakai untuk mencegah perubahan email oleh sesi yang
 * dibajak; email wajib unik selain milik pengguna sendiri.
 */
class UpdateEmailRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()?->getKey()),
            ],
            'current_password' => ['required', 'current_password'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.lowercase' => 'Email harus menggunakan huruf kecil.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini salah.',
        ];
    }
}
