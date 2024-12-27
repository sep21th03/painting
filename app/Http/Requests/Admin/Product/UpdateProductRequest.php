<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:products,id',
            'name' => 'required|string|max:255',
            'info' => 'nullable|string',
            'description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'setcategory' => "required|integer|exists:products,set_category_id|exists:setcategories,id",
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
            'id.required' => 'Vui lòng nhập ID sản phẩm.',
            'id.integer' => 'ID sản phẩm phải là một số nguyên.',
            'id.exists' => 'Sản phẩm không tồn tại.',
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string' => 'Tên sản phẩm phải là một chuỗi.',
            'name.max' => 'Tên sản phẩm không được quá 255 ký tự.',
            'discount.numeric' => 'Giá trị giảm giá phải là một số.',
            'discount.min' => 'Giá trị giảm giá không được nhỏ hơn 0.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(jsonResponse('error', $errors));
    }
}
