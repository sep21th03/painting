<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory;
    protected $table = 'sets';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function categories()
    {
        return $this->hasMany(SetCategory::class);
    }
}
