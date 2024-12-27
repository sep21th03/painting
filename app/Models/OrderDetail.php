<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = "order_details";
    public $timestamps = false;

    protected $fillable = ['order_id', 'product_hex_id', 'size_id', 'quantity', 'price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productHex()
    {
        return $this->belongsTo(ProductHex::class, 'product_hex_id');
    }
}
