<?php

namespace App\Http\Requests\Ujian;

// use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JadwalUjianRequest extends FormRequest
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
            'ujian_id'                   => 'required|exists:ujians,id',
            'mata_pelajaran_id'          => 'nullable|exists:mata_pelajarans,id',
            'nama_mata_pelajaran_custom' => 'nullable|string|max:255',
            'tanggal_ujian'              => 'required|date',
            'waktu_mulai'                => 'required|date_format:H:i',
            'waktu_selesai'              => 'required|date_format:H:i|after:waktu_mulai',
            'level_id'                 => 'nullable|exists:levels,id',
            'ustadz_id'                  => 'nullable|exists:ustadzs,id',
        ];
    }
}
