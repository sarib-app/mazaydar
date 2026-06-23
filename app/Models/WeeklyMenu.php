<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WeeklyMenu extends Model {
    protected $table = 'weekly_menu';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id','day_short','day_full','meals'];
    protected $casts = ['meals' => 'array'];
}
