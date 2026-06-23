<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MenuItem extends Model {
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id','name','category','image','calories','tags','description','protein','carbs','fat','ingredients','options'];
    protected $casts = ['tags' => 'array','ingredients' => 'array','options' => 'array'];
}
