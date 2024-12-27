<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = "orders";

    protected $guarded = ['id'];
    public const STATUS_WAITING = 0;
    public const STATUS_SUCCESS = 1;
    public const STATUS_CANCEL = 2;
    public const STATUS_CONFIRM = 3;
    public const STATUS_SHIPPING = 4;
    public const STATUS_WAITING_PAYMENT = 5;

    public const STATUS_LABEL = [
        self::STATUS_WAITING => 'Đang chờ',
        self::STATUS_SUCCESS => 'Thành công',
        self::STATUS_CANCEL => 'Đã hủy',
        self::STATUS_CONFIRM => 'Đã xác nhận',
        self::STATUS_SHIPPING => 'Đang giao hàng',
        self::STATUS_WAITING_PAYMENT => 'Đang chờ thanh toán',
    ];

    public const PAYMENT_LABEL = [
        0 => 'Thanh toán qua VNPAY',
        1 => 'Thanh toán khi nhận hàng',
    ];

    public static function getPaymentMethod($id)
    {
        return self::PAYMENT_LABEL[$id];
    }
    public static function getStatus($id)
    {
        return self::STATUS_LABEL[$id];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * Get all of the orderDetai for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
}
