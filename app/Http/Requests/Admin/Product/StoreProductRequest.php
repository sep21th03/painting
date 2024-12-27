<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'info' => 'nullable|string',
            'description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'setcategory_select' => 'required|integer',
            'sizeProduct' => 'required|string',
            'code' => 'required|string|unique:product_hex,hex_code',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', 
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
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string' => 'Tên sản phẩm phải là một chuỗi.',
            'name.max' => 'Tên sản phẩm không được quá 255 ký tự.',
            'info.string' => 'Thông tin sản phẩm phải là một chuỗi.',
            'setcategory_select.required' => 'Vui lòng chọn loại danh mục sản phẩm.',
            'setcategory_select.integer' => 'Loại danh mục sản phẩm phải là một số nguyên.',
            'setcategory_select.exists' => 'Loại danh mục sản phẩm không tồn tại.',
            'discount.numeric' => 'Giá trị giảm giá phải là một số.',
            'discount.min' => 'Giá trị giảm giá không được nhỏ hơn 0.',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'stock.integer' => 'Số lượng tồn kho phải là một số nguyên.',
            'stock.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá sản phẩm phải là một số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.',
            'code.required' => 'Vui lòng nhập mã sản phẩm.',
            'code.unique' => 'Mã sản phẩm đã tồn tại. Vui lòng nhập mã khác.',
            'sizeProduct.required' => 'Vui lòng nhập size',
            'gallery.image' => 'File phải là một ảnh.',
            'gallery.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, gif, svg hoặc webp.',
            'gallery.max' => 'Kích thước ảnh không được lớn hơn 2MB.',
        ];
    }
}
