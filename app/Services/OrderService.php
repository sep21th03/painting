<?php

namespace App\Services;

use App\Jobs\UpdateOrderStatusJob;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderService extends BaseService
{
    public function setModel()
    {
        return new Order();
    }

    public function getListByUser()
    {
        $user = Auth()->user();
        return $this->model->where('user_id', $user->id)->orderBy('id', 'desc')->get()->map(function ($order) {
            return [
                'id' => $order->id,
                'code' => $order->code,
                'created_at' => $order->created_at->format('H:i d/m/Y '),
                'status' => Order::STATUS_LABEL[$order->status] ?? 'Đang chờ',
                'total_price' => $order->total_price,
            ];
        });
    }
    public function getDetailOrder($id)
    {
        $order = $this->model->find($id);
        return [
            '$id' => $order->id,
            'code' => $order->code,
            'name' => $order->user_name,
            'user' => $order->user,
            'phone' => $order->phone,
            'status_label' => Order::STATUS_LABEL[$order->status] ?? 'Đang chờ',
            'status' => $order->status,
            'payment_method' => Order::PAYMENT_LABEL[$order->payment_method] ?? 'Lỗi',
            'total_price' => $order->total_price,
            'note'  => $order->note,
            'message' => $order->message,
            'created_at' => $order->created_at->format('H:i d/m/Y '),
            'order_details' => $order->orderDetails->map(function ($orderDetail) {
                return [
                    'id' => $orderDetail->id,
                    'product_name' => $orderDetail->productVariant->product->title . ' (Màu ' . $orderDetail->productVariant->color . ')',
                    'quantity' => $orderDetail->quantity,
                    'price' => $orderDetail->price
                ];
            }),
            'address' => $order->address
        ];
    }
    public function store($data)
    {
        DB::beginTransaction();
        try {
            $user = Auth()->user();
            $orderData = [
                'code' => 'MB' . $user->id . 'P' . time() . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2),
                'user_id' => $user->id,
                'user_name' => $data['first_name'] . ' ' . $data['last_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'] ?? '',
                'message' => $data['message'] ?? '',
                'total_price' => $data['total_price'],
                'payment_method' => $data['payment_method'],
                'shipping_fee' => $data['shipping_fee'] ?? 20000,
                'status' =>  $data['payment_method'] == 0 ? $this->model::STATUS_WAITING_PAYMENT : $this->model::STATUS_WAITING,
            ];
            $order = parent::store($orderData);
            $order_details = [];
            foreach ($data['order_details'] as $item) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if (!$variant) {
                    throw new \Exception("Product variant not found: " . $item['product_variant_id']);
                }
                if ($variant->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product variant: " . $item['product_variant_id']);
                }
                $variant->stock -= $item['quantity'];
                $variant->save();

                $order_details[] = [
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }
            $order->orderDetails()->createMany($order_details);
            $cartIds = [];
            foreach ($data['order_details'] as $item) {
                $cartIds[] = $item['id'];
            }
            Cart::whereIn('id', $cartIds)->delete();
            DB::commit();
            dispatch(new UpdateOrderStatusJob($user->email, 'Bạn đã đặt hàng thành công, xin vui lòng đợi xác nhận. Mã đơn hàng của bạn là: ', $order->code));
            return [
                'code' => $order->code,
                'total_price' => $order->total_price,
                'created_at' => $order->created_at->format('H:i d/m/Y ')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'error' => $e->getMessage()
            ];
        }
    }
    public function update($id, $data)
    {
        $order = $this->model->find($id);

        if (!$order) {
            return false;
        }

        if ($order->status != $data['status']) {
            $content = '';
            switch ($data['status']) {
                case $this->model::STATUS_CANCEL:
                    $content = "Đơn hàng #{$order->code} của bạn đã bị hủy bỏ";
                    break;
                case $this->model::STATUS_WAITING:
                    $content = "Đơn hàng #{$order->code} của bạn đang chờ xác nhận";
                    break;
                case $this->model::STATUS_SUCCESS:
                    $content = "Đơn hàng #{$order->code} của bạn đã được giao thành công";
                    break;
                case $this->model::STATUS_CONFIRM:
                    $content = "Đơn hàng #{$order->code} đã được xác nhận và sẽ sớm được giao tới bạn";
                    break;
                case $this->model::STATUS_SHIPPING:
                    $content = "Đơn hàng #{$order->code} đang trên đường giao tới bạn";
                    break;
                case $this->model::STATUS_WAITING_PAYMENT:
                    $content = "Đơn hàng #{$order->code} đang đang chờ thanh toán, hãy thanh toán đơn hàng ngay";
                    break;
            }
            if ($content) {
                dispatch(new UpdateOrderStatusJob($order->user->email, $order->user_name, $content));
            }
        }
        return $order->update($data);
    }
    public function userUpdate($id, $data)
    {
        $order = $this->model->find($id);
        if ($order->status !== $this->model::STATUS_WAITING) {
            return false;
        }
        return parent::update($id, $data);
    }
    public function delete($id)
    {
        $order = $this->model->find($id);
        if ($order->status !== $this->model::STATUS_WAITING) {
            return false;
        }
        $order->orderDetails()->delete();
        return $order->delete();
    }

    public function getOrderStatusSuccess()
    {
        return Order::where('status', 1)->count();
    }
    public function getOrderStatusWaiting()
    {
        return Order::where('status', 0)->count();
    }
    public function getOrderLast()
    {
        $sevenDaysAgo = now()->subDays(7);

        $totalOrders = Order::where('created_at', '>=', $sevenDaysAgo)->count();

        $completedOrders = Order::where('created_at', '>=', $sevenDaysAgo)
            ->where('status', Order::STATUS_SUCCESS)
            ->count();

        $pendingOrders = Order::where('created_at', '>=', $sevenDaysAgo)
            ->whereNotIn('status', [Order::STATUS_SUCCESS, Order::STATUS_CANCEL])
            ->count();

        $completedPercentage = $totalOrders ? round(($completedOrders / $totalOrders) * 100) : 0;
        $pendingPercentage = $totalOrders ? round(($pendingOrders / $totalOrders) * 100) : 0;

        $startOfLastWeek = now()->subWeek()->startOfWeek();
        $endOfLastWeek = now()->subWeek()->endOfWeek();
        $ordersLastWeek = Order::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        $startOfThisWeek = now()->startOfWeek();
        $endOfThisWeek = now()->endOfWeek();
        $ordersThisWeek = Order::whereBetween('created_at', [$startOfThisWeek, $endOfThisWeek])->count();

        $orderChangePercentage = $ordersLastWeek ? (($ordersThisWeek - $ordersLastWeek) / $ordersLastWeek) * 100 : 0;
        return [
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'pendingOrders' => $pendingOrders,
            'completedPercentage' => $completedPercentage,
            'pendingPercentage' => $pendingPercentage,
            'ordersLastWeek' => $ordersLastWeek,
            'ordersThisWeek' => $ordersThisWeek,
            'orderChangePercentage' => round($orderChangePercentage, 2)
        ];
    }

    public function getCompletedOrdersComparison()
    {
        $currentMonth = Carbon::now()->month;
        $previousMonth = Carbon::now()->subMonth()->month;
        $currentYear = Carbon::now()->year;

        $currentMonthOrders = [];
        $previousMonthOrders = [];

        $daysInCurrentMonth = Carbon::now()->daysInMonth;
        for ($day = 1; $day <= $daysInCurrentMonth; $day++) {
            $date = Carbon::create($currentYear, $currentMonth, $day);
            $currentMonthOrders[$day] = Order::where('status', Order::STATUS_SUCCESS)
                ->whereDate('created_at', $date)
                ->count();
        }

        $daysInPreviousMonth = Carbon::now()->subMonth()->daysInMonth;
        for ($day = 1; $day <= $daysInPreviousMonth; $day++) {
            $date = Carbon::create($currentYear, $previousMonth, $day);
            $previousMonthOrders[$day] = Order::where('status', Order::STATUS_SUCCESS)
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            'currentMonthOrders' => $currentMonthOrders,
            'previousMonthOrders' => $previousMonthOrders,
        ];
    }
}
