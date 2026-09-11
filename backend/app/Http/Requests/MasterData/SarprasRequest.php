<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SarprasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sarprasId = $this->route('sarpra') ?? $this->route('sarpras') ?? $this->route('id') ?? $this->id;
        if (is_object($sarprasId)) {
            $sarprasId = $sarprasId->id;
        }

        return [
            'kode_sarpras'      => [
                'required',
                'string',
                'max:50',
                Rule::unique('sarpras', 'kode_sarpras')->ignore($sarprasId)
            ],
            'nama_sarpras'      => 'required|string|max:150',
            'kategori'          => 'required|string|max:50',
            'gedung_id'         => 'nullable|exists:gedungs,id',
            'ruangan_id'        => 'nullable|exists:ruangans,id',
            'jumlah'            => 'required|integer|min:1|max:10000',
            'satuan'            => 'required|string|max:30',
            'kondisi'           => 'required|in:tersedia,rusak_ringan,rusak_berat,rusak',
            'sumber_dana'       => 'nullable|string|max:100',
            'tanggal_pengadaan' => 'nullable|date',
            'keterangan'        => 'nullable|string|max:1000',
            'foto'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_sarpras'      => 'Kode Barang / Sarpras',
            'nama_sarpras'      => 'Nama Sarana & Prasarana',
            'kategori'          => 'Kategori Sarpras',
            'gedung_id'         => 'Gedung',
            'ruangan_id'        => 'Ruangan',
            'jumlah'            => 'Jumlah',
            'satuan'            => 'Satuan',
            'kondisi'           => 'Status Kondisi',
            'sumber_dana'       => 'Sumber Dana',
            'tanggal_pengadaan' => 'Tanggal Pengadaan',
            'keterangan'        => 'Keterangan',
            'foto'              => 'Foto Barang',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Data tidak valid',
            'errors'  => $validator->errors()
        ], 422));
    }
}
