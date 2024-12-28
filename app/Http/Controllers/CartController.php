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
     * @group Cart Management
     * 
     * @response {
     *  "data": [
     *  {
     *     "id": 3,
     *     "product_name": "Tranh treo tường tráng gương SET1 của REICHTUM - Decor phòng ngủ/ Chất vải canvas/ Độ bền cao",
     *     "product_id": 1,
     *     "product_hex_id": 1,
     *     "code": "R287",
     *     "set": "SET 1 CÂY CỎ",
     *    "quantity": 2,
     *    "discount": 10,
     *    "size": "40X60 CANVAS",
     *     "current_price": 22540,
     *     "discounted_price": 20286,
     *     "images": [
     *        "assets/img/products/btbhGacYF1_1735273573.webp",
     *       "assets/img/products/fzPmcN7ma8_1735273573.webp",
     *      "assets/img/products/pRLgncKt2K_1735273573.webp"
     * ],
     *   "total_amount": 40572
     *      }
     * ],
     *"status": "success",
     *"message": "Giỏ hàng"
     * }
     */

    public function getMyCart()
    {
        $myCart = $this->cartService()->findByUserId(Auth::user()->id);
        return jsonResponse('success', 'Giỏ hàng', $myCart);
    }
    /**
     * Cập nhật giỏ hàng của người dùng hiện tại.
     *
     * @group Cart Management
     * 
     * @bodyParam userId
     * @bodyParam product_hex_id int required ID của sản phẩm trong giỏ hàng.
     * @bodyParam size_id int required ID của kích thước sản phẩm.
     * @bodyParam quantity int required Số lượng sản phẩm. Example: 3
     * 
     * @response {
     *   "status": "success",
     *   "message": "Cập nhật thành công!",
     *   "data": []
     * }
     */
    public function updateMyCart(UpdateCartRequest $request)
    {
        $data = $request->validated();
        $myCart = $this->cartService()->updateQuantityByUserId(Auth::user()->id, $data);
        return jsonResponse($myCart ? 'success' : 'error', $myCart ? 'Cập nhật thành công!' : 'Có lỗi xảy ra, xin vui lòng tải lại trang và thử lại.', $myCart);
    }
    /**
     * Thêm sản phẩm vào giỏ hàng.
     *
     * @group Cart Management
     * 
     * @bodyParam userId
     * @bodyParam product_hex_id int required ID của sản phẩm. Example: 10
     * @bodyParam size_id int required ID của kích thước sản phẩm. Example: 2
     * @bodyParam quantity int required Số lượng sản phẩm. Example: 1
     * 
     * @response {
     *   "status": "success",
     *   "message": "Cập nhật thành công!",
     *   "data": []
     * }
     */
    public function addByMyCart(UpdateCartRequest $request)
    {
        $data = $request->validated();
        $myCart = $this->cartService()->addByUserId(Auth::user()->id, $data);
        return jsonResponse($myCart ? 'success' : 'error', $myCart ? 'Cập nhật thành công!' : 'Có lỗi xảy ra, xin vui lòng tải lại trang và thử lại.', $myCart);
    }

    public function cartService()
    {
        return app(CartService::class);
    }
}
