<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class KategoriKegiatanRequest extends FormRequest
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
        $ketegoriId = $this->route('kategori_kegiatan') ?? $this->route('kategori-kegiatan') ?? $this->route('id') ?? $this->id;
        if (is_object($ketegoriId)) $ketegoriId = $ketegoriId->id;

        return [
            'nama_kategori' => ['required', 'string', 'max:50', Rule::unique('kategori_kegiatans', 'nama_kategori')->ignore($ketegoriId)],
            'kode_warna'  => 'required',
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
