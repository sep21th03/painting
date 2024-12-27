<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCategoryRequest extends FormRequest
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
            'id' => 'required|exists:sets,id',
            'name' => 'required|string|max:255|unique:sets,name,' . $this->id, // Ignore self
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Vui lòng chọn danh mục',
            'id.exists' => 'Danh mục không tồn tại',
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'name.max' => 'Tên danh mục tối đa 255 ký tự',
            'name.string' => 'Tên danh mục phải là chuỗi',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(jsonResponse('error', $errors));
    }
}
