<?php

namespace App\Http\Requests\Admin\ProductHex;


use Illuminate\Foundation\Http\FormRequest;

class UpdateProductHexRequest extends FormRequest
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
            'code' => 'required|integer',
            'size' => 'required|integer',
            'stock' => 'required|integer|max:255',
            'price' => 'required|string|max:255',
            'gallery.*' => 'image|mimes:jpg,png,jpeg,gif,webp|max:2048',
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
            'stock.required' => 'Mã không được bỏ trống.',
            'price.required' => 'Mã không được bỏ trống.',
            'gallery.image' => 'File phải là một ảnh.',
            'gallery.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, gif, svg hoặc webp.',
            'gallery.max' => 'Kích thước ảnh không được lớn hơn 2MB.',
        ];
    }
}
