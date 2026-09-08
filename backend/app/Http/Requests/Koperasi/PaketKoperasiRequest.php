<?php

namespace App\Http\Requests\Koperasi;

use Illuminate\Foundation\Http\FormRequest;

class PaketKoperasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('paket') ? $this->route('paket')->id : $this->id;

        return [
            'kode_paket' => 'nullable|string|max:60|unique:paket_koperasis,kode_paket,' . $id,
            'nama_paket' => 'required|string|max:150',
            'level_id' => 'nullable|exists:levels,id',
            'tingkat_id' => 'nullable|exists:tingkats,id',
            'harga_paket' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'deskripsi' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk_koperasis,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_paket.required' => 'Nama paket bundling wajib diisi.',
            'harga_paket.required' => 'Harga jual paket wajib diisi.',
            'items.required' => 'Minimal tambahkan 1 produk komponen di dalam paket.',
            'items.min' => 'Minimal tambahkan 1 produk komponen di dalam paket.',
            'items.*.produk_id.required' => 'Produk komponen paket wajib dipilih.',
            'items.*.jumlah.min' => 'Jumlah produk komponen minimal 1.',
        ];
    }
}
