<?php

namespace App\Http\Requests\PengaturanMenu;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request.
     */
    public function rules(): array
    {
        $roleId = $this->route('role') ?? $this->route('id') ?? $this->id;
        if (is_object($roleId)) {
            $roleId = $roleId->id;
        }

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                // Pengecekan unik, tapi abaikan ID saat mode Edit (jika nanti ada fitur edit role)
                Rule::unique('roles', 'name')->ignore($roleId)
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
