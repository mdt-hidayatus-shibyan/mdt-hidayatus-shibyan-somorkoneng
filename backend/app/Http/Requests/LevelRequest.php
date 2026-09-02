<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class LevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $levelId = $this->route('level') ?? $this->route('id') ?? $this->id;
        if (is_object($levelId)) {
            $levelId = $levelId->id;
        }
        return [
            'nama_level' => 'required|string|max:10',
            'tingkat_id' => 'required|exists:tingkats,id',
            'is_active'  => 'nullable|boolean',
            'urutan_level'     => ['required', 'int', 'max:50', Rule::unique('levels', 'urutan_level')->ignore($levelId)],
        ];
    }

    /**
     * KUNCI ANTI-REDIRECT AJAX
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Data tidak valid',
            'errors'  => $validator->errors()
        ], 422));
    }
}
