<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHex extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'product_hex';
    protected $fillable = ['product_id', 'hex_code'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }
    public function galleries()
    {
        return $this->hasMany(ProductGalleries::class);
    }
}
