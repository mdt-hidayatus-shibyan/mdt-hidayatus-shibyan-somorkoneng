<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SemesterRequest extends FormRequest
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
        $semesterId = $this->route('id') ?? $this->route('semester') ?? $this->id;
        if (is_object($semesterId)) {
            $semesterId = $semesterId->id;
        }
        $tahunPelajaranId = $this->tahun_pelajaran_id ?? $this->tahun_id;

        return [
            'tahun_pelajaran_id' => [
                'required',
                'exists:tahun_pelajarans,id'
            ],
            'nama_semester' => [
                'required',
                'in:Semester 1 (Ganjil),Semester 2 (Genap)',

                // Aturan pintar: Mencegah duplikat (Tidak boleh ada 2 Semester yang sama di 1 Tahun Pelajaran)
                // Fitur ignore() digunakan agar saat Edit Data, validasi unik ini tidak bentrok dengan datanya sendiri
                Rule::unique('semesters')->where(function ($query) use ($tahunPelajaranId) {
                    return $query->where('tahun_pelajaran_id', $tahunPelajaranId);
                })->ignore($semesterId)
            ],
            'tanggal_mulai' => [
                'required',
                'date'
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai'
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
