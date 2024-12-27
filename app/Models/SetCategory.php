<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetCategory extends Model
{
    use HasFactory;
    protected $table = 'setcategories';
    public $timestamps = false;
    protected $fillable = ['id', 'set_id', 'name'];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function productHex()
    {
        return $this->hasMany(ProductHex::class);
    }
}
