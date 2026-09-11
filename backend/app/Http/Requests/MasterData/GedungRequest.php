<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GedungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gedungId = $this->route('gedung') ?? $this->route('id') ?? $this->id;
        if (is_object($gedungId)) {
            $gedungId = $gedungId->id;
        }

        return [
            'kode_gedung'   => [
                'required',
                'string',
                'max:20',
                Rule::unique('gedungs', 'kode_gedung')->ignore($gedungId)
            ],
            'nama_gedung'   => 'required|string|max:100',
            'jumlah_lantai' => 'required|integer|min:1|max:20',
            'keterangan'    => 'nullable|string|max:500',
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_gedung'   => 'Kode Gedung',
            'nama_gedung'   => 'Nama Gedung',
            'jumlah_lantai' => 'Jumlah Lantai',
            'keterangan'    => 'Keterangan',
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
