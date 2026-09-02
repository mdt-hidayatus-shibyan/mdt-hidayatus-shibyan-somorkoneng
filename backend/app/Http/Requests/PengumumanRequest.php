<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PengumumanRequest extends FormRequest
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
        $pengumumanId = $this->route('pengumuman') ?? $this->route('id');
        if (is_object($pengumumanId)) $pengumumanId = $pengumumanId->id;
        return [
            'judul'           => 'required|string|max:255',
            'konten'          => 'required|string',
            'tipe'            => 'required|in:Informasi,Penting,Kegiatan,Libur',
            'target_audience' => 'required|in:Semua,Wali Murid,Ustadz',
            'status'          => 'required|in:Draft,Terbit,Arsip',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ];
    }
}
