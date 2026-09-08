<?php

namespace App\Http\Requests\Koperasi;

use Illuminate\Foundation\Http\FormRequest;

class TransaksiKasirRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_pelanggan' => 'required|in:Murid,Ustadz,Umum',
            'murid_id' => 'nullable|required_if:jenis_pelanggan,Murid|exists:murids,id',
            'ustadz_id' => 'nullable|required_if:jenis_pelanggan,Ustadz|exists:ustadzs,id',
            'nama_pelanggan_umum' => 'nullable|string|max:150',
            'metode_pembayaran' => 'required|in:Tunai,QRIS,Transfer,Potong_Tabungan,Hutang',
            'nominal_bayar' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.tipe' => 'required|in:Produk,Paket_Bundling',
            'items.*.produk_id' => 'nullable|required_if:items.*.tipe,Produk|exists:produk_koperasis,id',
            'items.*.paket_id' => 'nullable|required_if:items.*.tipe,Paket_Bundling|exists:paket_koperasis,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'items.*.diskon_item' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Keranjang belanja kasir masih kosong.',
            'items.min' => 'Keranjang belanja kasir minimal berisi 1 item.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'murid_id.required_if' => 'Data murid pembeli wajib dipilih jika kategori pelanggan adalah Murid.',
            'ustadz_id.required_if' => 'Data ustadz pembeli wajib dipilih jika kategori pelanggan adalah Ustadz.',
        ];
    }
}
