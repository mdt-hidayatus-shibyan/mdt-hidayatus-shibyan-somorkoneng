<?php

namespace App\Http\Requests\Koperasi;

use Illuminate\Foundation\Http\FormRequest;

class PembelianKoperasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier' => 'required|string|max:150',
            'nomor_faktur_supplier' => 'nullable|string|max:100',
            'tanggal' => 'required',
            'metode_pembayaran' => 'required|in:Tunai_Kas,Transfer_Bank,Hutang_Tempo,Hutang_Supplier,Hutang',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'nominal_bayar' => 'nullable|numeric|min:0',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'update_harga_produk' => 'nullable',
            'foto_faktur' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'catatan' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk_koperasis,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_beli' => 'nullable|numeric|min:0',
            'items.*.harga_beli_satuan' => 'nullable|numeric|min:0',
            'items.*.harga_jual' => 'nullable|numeric|min:0',
            'items.*.harga_jual_satuan' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier.required' => 'Nama supplier / toko grosir wajib diisi.',
            'tanggal.required' => 'Tanggal pembelian wajib diisi.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'items.required' => 'Keranjang kulakan minimal berisi 1 barang.',
            'items.min' => 'Keranjang kulakan minimal berisi 1 barang.',
            'items.*.produk_id.required' => 'Produk barang wajib dipilih.',
            'items.*.jumlah.min' => 'Jumlah kuantitas beli minimal 1.',
            'items.*.harga_beli_satuan.required' => 'Harga beli modal satuan wajib diisi.',
            'foto_faktur.image' => 'File bukti faktur harus berupa gambar (JPG, PNG, WebP).',
            'foto_faktur.max' => 'Ukuran file bukti faktur maksimal 5 MB.',
        ];
    }
}
