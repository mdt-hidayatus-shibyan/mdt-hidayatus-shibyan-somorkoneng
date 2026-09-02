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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
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

            // Validasi untuk array permissions (Select2 Multiple)
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string'], // Memastikan setiap ID permission yang dipilih valid di database
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
