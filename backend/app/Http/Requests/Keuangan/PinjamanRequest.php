<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PinjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun_pelajaran_id' => 'nullable|exists:tahun_pelajarans,id',
            'nasabah_pinjaman_id' => 'required|exists:nasabah_pinjamans,id',
            'akun_keuangan_id' => 'required|exists:akun_keuangans,id',
            'bank_id' => 'nullable|exists:banks,id',
            'nominal_pinjaman' => 'required|numeric|min:50000',
            'biaya_administrasi' => 'nullable|numeric|min:0',
            'tenor_bulan' => 'required|integer|min:1|max:60',
            'margin_infaq_persen' => 'nullable|numeric|min:0|max:100',
            'tanggal_pengajuan' => 'required|date',
            'keperluan_pinjaman' => 'required|string|max:500',
            'catatan' => 'nullable|string',

            // Data Jaminan / Agunan
            'jaminan' => 'nullable|array',
            'jaminan.*.jenis_jaminan' => 'nullable|in:bpkb_motor,bpkb_mobil,sertifikat_tanah,emas_perhiasan,ijazah,buku_tabungan,elektronik,lainnya',
            'jaminan.*.nama_barang_jaminan' => 'nullable|string|max:200',
            'jaminan.*.nomor_dokumen_jaminan' => 'nullable|string|max:100',
            'jaminan.*.atas_nama_dokumen' => 'nullable|string|max:150',
            'jaminan.*.taksiran_nilai' => 'nullable|numeric|min:0',
            'jaminan.*.deskripsi_kondisi' => 'nullable|string',
            'jaminan.*.lokasi_penyimpanan' => 'nullable|string|max:150',
            'jaminan.*.foto_dokumen' => 'nullable|image|max:4096',
            'jaminan.*.foto_barang' => 'nullable|image|max:4096',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Data pengajuan pinjaman tidak valid',
                'errors'  => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
