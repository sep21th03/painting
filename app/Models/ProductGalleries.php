<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGalleries extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'galleries';
    protected $fillable = ['product_hex_id', 'image_path'];

    public function productHex()
    {
        return $this->belongsTo(Product::class);
    }

}
