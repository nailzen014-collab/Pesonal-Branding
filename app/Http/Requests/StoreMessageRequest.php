<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form pesan dari pengunjung (FR-09).
 *
 * Proteksi spam sederhana:
 * - honeypot: field `website` disembunyikan dari manusia. Bot biasa
 *   mengisinya otomatis, jadi nilainya harus kosong.
 * - throttle: membatasi jumlah request per menit (dipasang di route).
 */
class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'min:10', 'max:2000'],
            'website' => ['nullable', 'size:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'subject.required' => 'Subjek wajib diisi.',
            'body.required' => 'Pesan wajib diisi.',
            'body.min' => 'Pesan minimal 10 karakter.',
            'body.max' => 'Pesan maksimal 2000 karakter.',
            'website.size' => 'Pengiriman ditolak sebagai spam.',
        ];
    }

    /**
     * Nama atribut untuk pesan error agar tidak membocorkan nama field honeypot.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'subject' => 'subjek',
            'body' => 'pesan',
        ];
    }
}
