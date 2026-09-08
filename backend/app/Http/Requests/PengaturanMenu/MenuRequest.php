<?php

namespace App\Http\Requests\PengaturanMenu;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
// use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name'     => trim($this->name ?? ''),
            'url'      => trim($this->url ?? ''),
            'category' => $this->category ? trim(strtoupper($this->category)) : null,
            'icon'     => $this->icon ? trim($this->icon) : 'bi-circle',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'url'          => ['required', 'string', 'max:255'],
            'category'     => ['nullable', 'string', 'max:100'],
            'icon'         => ['nullable', 'string', 'max:50'],
            'orders'       => ['nullable', 'integer', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
            'main_menu_id' => ['nullable', 'exists:menus,id'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ];
    }

    /**
     * Custom message validation
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'Nama menu wajib diisi.',
            'name.max'             => 'Nama menu maksimal 100 karakter.',
            'url.required'         => 'URL / Route menu wajib diisi.',
            'url.max'              => 'URL / Route maksimal 255 karakter.',
            'main_menu_id.exists'  => 'Induk menu yang dipilih tidak valid.',
            'orders.integer'       => 'Nomor urutan harus berupa angka bulat.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validasi gagal, silakan periksa inputan Anda.',
                'errors'  => $validator->errors()
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
