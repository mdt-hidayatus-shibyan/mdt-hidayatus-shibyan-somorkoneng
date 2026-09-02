<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class TingkatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tingkatId = $this->route('tingkat') ?? $this->route('id') ?? $this->id;
        if (is_object($tingkatId)) {
            $tingkatId = $tingkatId->id;
        }

        return [
            'kode_tingkat' => [
                'required',
                'string',
                'max:5',
                Rule::unique('tingkats', 'kode_tingkat')->ignore($tingkatId)
            ],
            'kode_mdt_tingkat' => [
                'required',
                'string',
                'max:5',
                Rule::unique('tingkats', 'kode_mdt_tingkat')->ignore($tingkatId)
            ],
            'urutan_tingkat'     => ['required', 'int', 'max:10', Rule::unique('tingkats', 'urutan_tingkat')->ignore($tingkatId)],
            'nama_tingkat'     => 'required|string|max:50',
            'nama_mdt_tingkat' => 'required|string|max:50',
            'kode_warna'       => 'required|string|max:7',
            'is_active'        => 'nullable|boolean'
        ];
    }

    /**
     * KUNCI ANTI-REDIRECT: Paksa Laravel membalas dengan JSON saat error.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Data tidak valid',
            'errors'  => $validator->errors()
        ], 422));
    }
}
