<?php

namespace App\Http\Requests\Koperasi;

use Illuminate\Foundation\Http\FormRequest;

class StokKoperasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produk_id' => 'required|exists:produk_koperasis,id',
            'tipe_aksi' => 'required|in:Masuk,Opname',
            'jumlah' => 'required_if:tipe_aksi,Masuk|nullable|integer|min:1',
            'harga_beli' => 'nullable|numeric|min:0',
            'stok_fisik' => 'required_if:tipe_aksi,Opname|nullable|integer|min:0',
            'referensi' => 'nullable|string|max:100',
            'keterangan' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'produk_id.required' => 'Produk wajib dipilih.',
            'jumlah.required_if' => 'Jumlah stok masuk wajib diisi.',
            'stok_fisik.required_if' => 'Jumlah stok fisik opname wajib diisi.',
            'keterangan.required' => 'Keterangan/alasan mutasi stok wajib diisi.',
        ];
    }
}
