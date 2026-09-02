<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ruanganId = $this->route('ruangan') ?? $this->route('id') ?? $this->id;
        if (is_object($ruanganId)) {
            $ruanganId = $ruanganId->id;
        }
        $tahunPelajaranId = $this->tahun_pelajaran_id ?? $this->tahun_id;

        return [
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajarans,id',
            'level_id'           => 'required|exists:levels,id',
            'ustadz_id'          => 'nullable|exists:ustadzs,id',
            'kapasitas'          => 'required|integer|min:1|max:100',
            'nama_ruangan'       => [
                'required',
                'string',
                'max:50',
                Rule::unique('ruangans', 'nama_ruangan')
                    ->where('tahun_pelajaran_id', $tahunPelajaranId)
                    ->ignore($ruanganId)
            ],
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
