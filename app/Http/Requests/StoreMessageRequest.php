<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form pesan dari pengunjung (FR-09).
 *
 * Nama dan email tidak divalidasi di sini karena diambil dari akun
 * Google pengunjung (guard "visitor") di ContactController.
 *
 * Proteksi spam:
 * - pengirim wajib login Google (middleware auth.visitor di route).
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
            'subject' => 'subjek',
            'body' => 'pesan',
        ];
    }
}
