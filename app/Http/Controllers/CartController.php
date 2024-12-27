<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cart\UpdateCartRequest;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
class CartController extends Controller
{
    /**
     * Lấy giỏ hàng của người dùng hiện tại.
     *
     * Phương thức này sử dụng `cartService` để tìm giỏ hàng của người dùng dựa trên 
     * ID của người dùng hiện tại được lấy từ `Auth::user()`.
     *
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON chứa thông tin giỏ hàng của người dùng.
     */
    public function getMyCart()
    {
        $myCart = $this->cartService()->findByUserId(Auth::user()->id);
        return jsonResponse('success', 'Giỏ hàng', $myCart);
    }
    /**
     * Cập nhật giỏ hàng của người dùng hiện tại.
     *
     * Phương thức này nhận yêu cầu cập nhật giỏ hàng từ người dùng, xác thực dữ liệu 
     * đầu vào bằng `UpdateCartRequest`, sau đó sử dụng `cartService` để cập nhật giỏ hàng 
     * dựa trên ID người dùng.
     *
     * @param UpdateCartRequest $request Yêu cầu chứa dữ liệu cần cập nhật giỏ hàng.
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON với thông báo cập nhật thành công hoặc thất bại.
     */
    public function updateMyCart(UpdateCartRequest $request)
    {
        $data = $request->validated();
        $myCart = $this->cartService()->updateQuantityByUserId(Auth::user()->id, $data);
        return jsonResponse($myCart ? 'success' : 'error',  $myCart ? 'Cập nhật thành công!' : 'Có lỗi xảy ra, xin vui lòng tải lại trang và thử lại.', $myCart);
    }
    public function addByMyCart(UpdateCartRequest $request)
    {
        $data = $request->validated();
        $myCart = $this->cartService()->addByUserId(Auth::user()->id, $data);
        return jsonResponse($myCart ? 'success' : 'error',  $myCart ? 'Cập nhật thành công!' : 'Có lỗi xảy ra, xin vui lòng tải lại trang và thử lại.', $myCart);
    }

    public function cartService()
    {
        return app(CartService::class);
    }
}
