<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['set_category_id', 'info', 'name', 'description', 'discount'];


    public function productHex()
    {
        return $this->hasMany(ProductHex::class, 'product_id');
    }

    public function categories()
    {
        return $this->belongsTo(SetCategory::class, 'set_category_id');
    }
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }
}
