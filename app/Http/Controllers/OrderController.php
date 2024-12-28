<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\StoreOrderRequest;
use App\Http\Requests\Api\Order\UpdateOrderRequest;
use App\Http\Requests\Api\Order\VnpayPaymentRequest;
use App\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use App\Services\OrderService;
use App\Models\Order;
/**
 * Class OrderController
 *
 * Controller quản lý các chức năng liên quan đến đơn hàng, bao gồm:
 * - Lấy danh sách đơn hàng
 * - Chi tiết đơn hàng
 * - Tạo, cập nhật, xóa đơn hàng
 * - Thanh toán qua VNPay
 *
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    /**
     * Lấy danh sách đơn hàng của người dùng hiện tại.
     *
     * Phương thức này sử dụng `orderService` để lấy danh sách đơn hàng của người dùng hiện tại.
     *
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON chứa danh sách đơn hàng.
     */
    public function getListByUser()
    {
        $result = $this->orderService->getListByUser();
        return jsonResponse('success', 'Danh sách đơn hàng', $result);
    }
    /**
     * Lấy chi tiết đơn hàng theo ID.
     *
     * Phương thức này sử dụng `orderService` để lấy chi tiết đơn hàng dựa trên ID được truyền vào.
     *
     * @param int $id ID của đơn hàng cần lấy chi tiết.
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON chứa thông tin chi tiết đơn hàng.
     */
    public function getDetailOrder($id)
    {
        $result = $this->orderService->getDetailOrder($id);
        return jsonResponse('success', 'Chi tiết đơn hàng', $result);
    }
    /**
     * Store a new order in the database.
     * 
     * This method handles the creation of an order, including the generation of the order code,
     * storing order details, updating product stock, and removing items from the cart.
     * It also dispatches a job to notify the user of the order status.
     *
     * @param array $data Array containing order information, including:
     *  - string 'first_name': First name of the user.
     *  - string 'last_name': Last name of the user.
     *  - string 'phone': User's phone number.
     *  - string 'address': Delivery address.
     *  - string|null 'note': Optional note for the order.
     *  - string|null 'message': Optional message for the order.
     *  - float 'total_price': Total price of the order.
     *  - int 'payment_method': Payment method (e.g., 0 for unpaid, 1 for paid).
     *  - float|null 'shipping_fee': Shipping fee (default: 20000).
     *  - array 'order_details': Array of order details, each containing:
     *      - int 'product_hex_id': ID of the product hex.
     *      - int 'size_id': ID of the product size.
     *      - int 'quantity': Quantity of the product.
     *      - float 'price': Price of the product.
     *      - int 'id': Cart ID of the product.
     * 
     * @return array Returns an array with either the order's code, total price, and creation time 
     * on success, or an error message on failure.
     * 
     * @throws \Exception If a product hex or size is not found, or if the stock is insufficient.
     */

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $result = $this->orderService->store($data);
        return $result
            ? jsonResponse('success', 'Tạo đơn hàng thành công', $result)
            : jsonResponse('error', 'Tạo đơn hàng thất bại', $result);
    }
    /**
     * Cập nhật đơn hàng.
     *
     * Phương thức này nhận yêu cầu cập nhật đơn hàng, xác thực dữ liệu bằng `UpdateOrderRequest`,
     * sau đó sử dụng `orderService` để cập nhật đơn hàng dựa trên ID.
     *
     * @param UpdateOrderRequest $request Yêu cầu chứa dữ liệu cần cập nhật cho đơn hàng.
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON về kết quả cập nhật đơn hàng.
     */
    public function update(UpdateOrderRequest $request)
    {
        $data = $request->validated();
        $result = $this->orderService->userUpdate($data['id'], $data);
        return $result
            ? jsonResponse('success', 'Cập nhật đơn hàng thành công', $result)
            : jsonResponse('error', 'Cập nhật đơn hàng thất bại', $result);
    }
    /**
     * Xóa đơn hàng theo ID.
     *
     * Phương thức này sử dụng `orderService` để xóa đơn hàng dựa trên ID được truyền vào.
     *
     * @param int $id ID của đơn hàng cần xóa.
     * @return \Illuminate\Http\JsonResponse Trả về phản hồi dạng JSON về kết quả xóa đơn hàng.
     */
    public function delete($id)
    {
        $result = $this->orderService->delete($id);
        return $result
            ? jsonResponse('success', 'Xóa đơn hàng thành công')
            : jsonResponse('error', 'Xóa đơn hàng thất bại');
    }
    public function updateStatus(UpdateOrderStatusRequest $request)
    {
        ob_start();
        $data = $request->validated();
        $result = $this->orderService->update($data['id'], $data);
        ob_end_clean();
        return $result
            ? jsonResponse('success', 'Cập nhật trạng thái đơn hàng thành công')
            : jsonResponse('error', 'Cập nhật trạng thái đơn hàng thất bại');
    }

    public function vnpayPaymentComplete(Request $request, $order_id)
    {
        $url_return = $request->url_return;
        $this->orderService->updateByCode($order_id, [
            'status' => Order::STATUS_SUCCESS,
        ]);

        return redirect($url_return);
    }
    /**
     * Thanh toán đơn hàng bằng VNPay.
     *
     * Phương thức này xử lý thanh toán bằng VNPay, tạo URL thanh toán dựa trên dữ liệu đơn hàng
     * và các thông tin bảo mật từ môi trường (env).
     * 
     * @param VnpayPaymentRequest $request Yêu cầu chứa dữ liệu cần thiết cho thanh toán VNPay.
     * @return void Trả về URL thanh toán VNPay dạng JSON.
     */
    public function vnpayPayment(VnpayPaymentRequest $request)
    {

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('order.vnpay_payment_complete', ['id' => $request->order_id]) . '?url_return=' . $request->url_return; // đường dẫn khi thành công
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_TxnRef = $request->order_id; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Thanh toán đơn hàng tại MobileSell";
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->amount * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        //Add Params of 2.0.1 Version
        // $vnp_ExpireDate = $_POST['txtexpire'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
            // "vnp_ExpireDate"=>$vnp_ExpireDate
        );
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret); //
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        );
        echo json_encode($returnData);
        // vui lòng tham khảo thêm tại code demo
    }
}
