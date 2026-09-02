<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MataPelajaranRequest extends FormRequest
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
        return [
            'level_id'   => ['required', 'exists:levels,id'],
            'kode_mapel' => ['required', 'string', 'max:50'], // Dibatasi 50 karakter agar rapi
            'nama_mapel' => ['required', 'string', 'max:255'],
            'kelompok'   => ['required', 'in:Wajib,Ekstra'],
            'referensi'  => ['nullable', 'string', 'max:255'],
            'pengarang'  => ['nullable', 'string', 'max:255'],
            'penerbit'   => ['nullable', 'string', 'max:255'],
            'is_active'  => ['boolean'], // Menerima true/false atau 1/0
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
