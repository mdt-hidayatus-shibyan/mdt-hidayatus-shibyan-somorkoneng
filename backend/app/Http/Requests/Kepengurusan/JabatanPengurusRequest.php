<?php

namespace App\Http\Requests\Kepengurusan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class JabatanPengurusRequest extends FormRequest
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
        $jabatanId = $this->route('jabatan_pengurus') ?? $this->route('jabatan-pengurus') ?? $this->route('jabatan_penguru') ?? $this->route('id') ?? $this->id;
        if (is_object($jabatanId)) {
            $jabatanId = $jabatanId->id;
        }
        return [
            'nama_jabatan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jabatan_pengurus', 'nama_jabatan')->ignore($jabatanId)
            ],
            'level' => [
                'nullable',
                'string',
                'max:100',

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
