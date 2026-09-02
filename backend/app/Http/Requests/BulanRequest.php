<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class BulanRequest extends FormRequest
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
        $bulanId = $this->route('id') ?? $this->route('bulan') ?? $this->id;
        if (is_object($bulanId)) {
            $bulanId = $bulanId->id;
        }
        $tahunPelajaranId = $this->tahun_pelajaran_id ?? $this->tahun_id;

        return [
            'tahun_hijriyah' => [
                'required',
                'string',
                'max:4'
            ],
            'nama_bulan' => [
                'required',
                'string',
                'max:50',
                // Mencegah nama bulan yang sama di dalam satu tahun pelajaran
                Rule::unique('bulan_hijriyahs')->where(function ($query) use ($tahunPelajaranId) {
                    return $query->where('tahun_pelajaran_id', $tahunPelajaranId);
                })->ignore($bulanId)
            ],
            'urutan' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                // Mencegah urutan yang sama di dalam satu tahun pelajaran
                Rule::unique('bulan_hijriyahs')->where(function ($query) use ($tahunPelajaranId) {
                    return $query->where('tahun_pelajaran_id', $tahunPelajaranId);
                })->ignore($bulanId)
            ],
            'tanggal_mulai_masehi' => [
                'required',
                'date'
            ],
            'tanggal_selesai_masehi' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai_masehi'
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
