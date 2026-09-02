<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RuanganBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajarans,id',
            'ruangan'                 => 'required|array|min:1',
            'ruangan.*.level_id'      => 'required|exists:levels,id',
            'ruangan.*.ustadz_id'    => 'nullable|exists:ustadzs,id',
            'ruangan.*.kapasitas'     => 'required|integer|min:1|max:100',

            // Logika Unik Ganda: Nama ruangan unik di dalam Tahun Pelajaran yang sama
            'ruangan.*.nama_ruangan'  => [
                'required',
                'string',
                'max:50',
                Rule::unique('ruangans', 'nama_ruangan')->where(function ($query) {
                    return $query->where('tahun_pelajaran_id', $this->tahun_pelajaran_id);
                })
            ],
        ];
    }
}
