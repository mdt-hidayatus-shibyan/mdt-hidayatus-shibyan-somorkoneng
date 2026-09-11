<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AkunKeuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('akun') ?? $this->route('id') ?? $this->id;
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'kode_akun' => [
                'required',
                'string',
                'max:50',
                Rule::unique('akun_keuangans', 'kode_akun')->ignore($id),
            ],
            'nama_akun' => 'required|string|max:150',
            'tipe_akun' => 'required|in:kas,bank,operasional,investasi,kewajiban,ekuitas',
            'saldo_awal' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Data tidak valid',
                'errors'  => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
