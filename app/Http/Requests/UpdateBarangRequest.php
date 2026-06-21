<?php

// File: app/Http/Requests/UpdateBarangRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarangRequest extends FormRequest
{
    /**
     * authorize(): Sama seperti StoreBarangRequest, admin boleh edit
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * rules(): Aturan validasi untuk edit barang
     *
     * Sedikit berbeda dengan Store:
     * - 'sometimes' digunakan agar field tidak wajib ada di request
     *   (karena user mungkin hanya ingin mengedit sebagian field saja)
     * - Foto tetap nullable karena boleh tidak ganti foto
     */
    public function rules(): array
    {
        return [
            'nama_barang' => ['sometimes', 'required', 'string', 'max:150'],
            'kategori'    => ['sometimes', 'required', 'string', 'max:100'],
            'jumlah_total'    => ['sometimes', 'required', 'integer', 'min:0'],
            'jumlah_tersedia' => ['sometimes', 'required', 'integer', 'min:0'],
            'kondisi'     => ['sometimes', 'required', 'in:baik,rusak_ringan,rusak_berat'],
            'keterangan'  => ['nullable', 'string'],
            'foto'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * messages(): Pesan error dalam Bahasa Indonesia (sama dengan Store)
     */
    public function messages(): array
    {
        return [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.max'      => 'Nama barang maksimal 150 karakter.',
            'kategori.required'    => 'Kategori wajib diisi.',
            'kategori.max'         => 'Kategori maksimal 100 karakter.',
            'jumlah_total.required'    => 'Jumlah total wajib diisi.',
            'jumlah_total.integer'     => 'Jumlah total harus berupa angka.',
            'jumlah_total.min'         => 'Jumlah total tidak boleh negatif.',
            'jumlah_tersedia.required' => 'Jumlah tersedia wajib diisi.',
            'jumlah_tersedia.integer'  => 'Jumlah tersedia harus berupa angka.',
            'jumlah_tersedia.min'      => 'Jumlah tersedia tidak boleh negatif.',
            'kondisi.required' => 'Kondisi wajib dipilih.',
            'kondisi.in'       => 'Kondisi tidak valid.',
            'foto.image'       => 'File harus berupa gambar.',
            'foto.mimes'       => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto.max'         => 'Ukuran foto maksimal 2MB.',
        ];
    }
}