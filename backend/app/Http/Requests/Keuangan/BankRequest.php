<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class BankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('bank') ?? $this->route('id') ?? $this->id;
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'nama_bank' => 'required|string|max:100',
            'kode_bank' => 'nullable|string|max:20',
            'nomor_rekening' => [
                'required',
                'string',
                'max:50',
                Rule::unique('banks', 'nomor_rekening')->ignore($id),
            ],
            'atas_nama' => 'required|string|max:150',
            'cabang' => 'nullable|string|max:150',
            'saldo' => 'nullable|numeric|min:0',
            'is_default' => 'nullable|boolean',
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
