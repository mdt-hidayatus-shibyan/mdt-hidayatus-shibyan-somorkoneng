<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class TahunPelajaranRequest extends FormRequest
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
        $tpId = $this->route('tahun_pelajaran') ?? $this->route('tahun-pelajaran') ?? $this->route('id') ?? $this->id;
        if (is_object($tpId)) {
            $tpId = $tpId->id;
        }

        return [
            'nama_hijriyah' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_pelajarans', 'nama_hijriyah')->ignore($tpId)
            ],
            'nama_masehi' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tahun_pelajarans', 'nama_masehi')->ignore($tpId)
            ],
            'is_active'        => 'nullable|boolean'
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
