<?php

namespace App\Http\Requests\Koperasi;

use Illuminate\Foundation\Http\FormRequest;

class ProdukKoperasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('produk') ? $this->route('produk')->id : $this->id;

        return [
            'kategori_id' => 'required|exists:kategori_produks,id',
            'kode_produk' => 'nullable|string|max:60|unique:produk_koperasis,kode_produk,' . $id,
            'nama_produk' => 'required|string|max:150',
            'satuan' => 'required|string|max:30',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'stok_minimum' => 'nullable|integer|min:0',
            'status' => 'required|in:Aktif,Nonaktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori produk wajib dipilih.',
            'kode_produk.unique' => 'Kode barcode / SKU produk ini sudah terdaftar.',
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'satuan.required' => 'Satuan produk wajib diisi.',
            'harga_beli.required' => 'Harga beli (HPP) wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
        ];
    }
}
