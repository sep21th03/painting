<?php

namespace App\Http\Requests\Admin\ProductSize;


use Illuminate\Foundation\Http\FormRequest;

class StoreProductSizeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Kiểm tra quyền của người dùng nếu cần
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'addhexID' => 'required|integer',
            'sizeName' => 'required|string|max:255',
            'priceSize' => 'required|string|max:255',
            'stockSize' => 'required|integer',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'sizeName.required' => 'Tên size không được bỏ trống.'
        ];
    }
}
