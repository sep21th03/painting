<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'product_sizes';

    protected $fillable = ['product_hex_id', 'size', 'price', 'stock'];

    public function productHex()
    {
        return $this->belongsTo(ProductHex::class);
    }
}
