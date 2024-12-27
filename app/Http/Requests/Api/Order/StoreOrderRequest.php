<?php

namespace App\Http\Requests\Api\Order;

use App\Rules\PhoneNumberRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => ['required', 'numeric', new PhoneNumberRule()],
            'address' => 'required',
            'note' => 'nullable',
            'message' => 'nullable',
            'payment_method' => 'required|in:0,1',
            'shipping_fee' => 'nullable',
            'order_details' => 'required|array',
            'order_details.*.id' => 'required',
            'order_details.*.product_hex_id' => 'required|exists:product_hex,id',
            'order_details.*.size_id' => 'required|exists:product_sizes,id',
            'order_details.*.price' => 'required|numeric',
            'order_details.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
        ];
    }
    function message() {
        return [
            'first_name.required' => 'Vui lòng nhập họ',
            'last_name.required' => 'Vui lòng nhập tên',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.numeric' => 'Số điện thoại không hợp lệ',
            'address.required' => 'Vui lòng nhập địa chỉ',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            'order_details.required' => 'Vui lòng chọn sản phẩm',
            'order_details.*.product_hex_id.required' => 'Vui lòng nhập sản phẩm',
            'order_details.*.product_hex_id.exists' => 'Sản phẩm không tồn tại',
            'order_details.*.size_id.required' => 'Vui lòng nhập sản phẩm',
            'order_details.*.size_id.exists' => 'Sản phẩm không tồn tại',
            'order_details.*.price.required' => 'Vui lòng nhập giá sản phẩm',
            'order_details.*.price.numeric' => 'Giá sản phẩm không hợp lệ',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        throw new HttpResponseException(jsonResponse('error', $errors));
    }
}
