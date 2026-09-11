<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransaksiKeuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun_pelajaran_id' => 'nullable|exists:tahun_pelajarans,id',
            'akun_keuangan_id' => 'required|exists:akun_keuangans,id',
            'kategori_keuangan_id' => 'nullable|exists:kategori_keuangans,id',
            'jenis_transaksi' => 'required|in:pemasukan,pengeluaran,mutasi,simpanan',
            'metode_pembayaran' => 'required|in:tunai,transfer_bank',
            'bank_id' => 'nullable|required_if:metode_pembayaran,transfer_bank|exists:banks,id',
            'akun_tujuan_id' => 'nullable|required_if:jenis_transaksi,mutasi|exists:akun_keuangans,id',
            'bank_tujuan_id' => 'nullable|exists:banks,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal_transaksi' => 'required|date',
            'nomor_referensi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
            'bukti_transaksi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Data transaksi tidak valid',
                'errors'  => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
