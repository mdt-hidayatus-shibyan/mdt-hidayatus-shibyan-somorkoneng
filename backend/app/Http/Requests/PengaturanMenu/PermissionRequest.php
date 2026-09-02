<?php

namespace App\Http\Requests\PengaturanMenu;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $permissionId = $this->route('permission') ?? $this->route('id') ?? $this->id;
        if (is_object($permissionId)) {
            $permissionId = $permissionId->id;
        }

        return [
            'name' => [
                'required',
                'string',
                'max:125',
                // Pengecekan unik, tapi abaikan ID saat mode Edit
                Rule::unique('permissions', 'name')->ignore($permissionId)
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
