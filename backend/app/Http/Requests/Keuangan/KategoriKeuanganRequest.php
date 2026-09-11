<?php

namespace App\Http\Requests\Keuangan;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class KategoriKeuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('parent_id') && !$this->filled('jenis')) {
            $parent = \App\Models\Keuangan\KategoriKeuangan::find($this->parent_id);
            if ($parent) {
                $this->merge(['jenis' => $parent->jenis]);
            }
        }
    }

    public function rules(): array
    {
        $id = $this->route('kategori') ?? $this->route('id') ?? $this->id;
        if (is_object($id)) {
            $id = $id->id;
        }

        return [
            'parent_id' => 'nullable|exists:kategori_keuangans,id',
            'kode_kategori' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kategori_keuangans', 'kode_kategori')->ignore($id),
            ],
            'nama_kategori' => 'required|string|max:150',
            'jenis' => 'required|in:pemasukan,pengeluaran,simpanan',
            'deskripsi' => 'nullable|string',
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
