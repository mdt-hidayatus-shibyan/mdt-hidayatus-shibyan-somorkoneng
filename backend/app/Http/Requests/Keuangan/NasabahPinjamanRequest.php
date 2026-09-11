<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class NasabahPinjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('nasabah') ?? $this->route('id') ?? $this->id;
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'kode_nasabah' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('nasabah_pinjamans', 'kode_nasabah')->ignore($id),
            ],
            'tipe_nasabah' => 'required|in:ustadz,pengurus,wali_murid,umum',
            'ustadz_id' => 'nullable|exists:ustadzs,id',
            'wali_murid_id' => 'nullable|exists:wali_murids,id',
            'user_id' => 'nullable|exists:users,id',
            'nama_lengkap' => 'required|string|max:150',
            'nik_ktp' => 'nullable|string|max:30',
            'no_hp' => 'required|string|max:30',
            'alamat' => 'required|string',
            'pekerjaan' => 'nullable|string|max:100',
            'foto_ktp' => 'nullable|image|max:3072',
            'foto_nasabah' => 'nullable|image|max:3072',
            'catatan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Data nasabah tidak valid',
                'errors'  => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
