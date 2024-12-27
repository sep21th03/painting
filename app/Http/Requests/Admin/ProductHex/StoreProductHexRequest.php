<?php

namespace App\Http\Requests\Admin\ProductHex;


use Illuminate\Foundation\Http\FormRequest;

class StoreProductHexRequest extends FormRequest
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
            'product_id' => 'required|integer',
            'nameHex' => 'required|string|max:255|unique:product_hex,hex_code',
            'sizeHex' => 'required|string|max:255',
            'priceHex' => 'required|string|max:255',
            'stockHex' => 'required|integer',
            'imageHex.*' => 'image|mimes:jpg,png,jpeg,gif,webp|max:2048',
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
            'product_id.required' => 'Mã sản phẩm không được b�� trống.',
            'product_id.integer' => 'Mã sản phẩm phải là một số.',
            'nameHex.required' => 'Mã không được bỏ trống.',
            'nameHex' => 'required|string|max:255|unique:product_hex,hex_code,' . $this->route('id'),
            'imageHex.image' => 'File phải là một ảnh.',
            'imageHex.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, gif, svg hoặc webp.',
            'imageHex.max' => 'Kích thước ảnh không được lớn hơn 2MB.',
        ];
    }
}
