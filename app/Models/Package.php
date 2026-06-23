<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Package extends Model {
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id','name','tagline','price','meals_per_day','total_meals','cycle_days','meal_types','features','is_popular'];
    protected $casts = ['meal_types' => 'array','features' => 'array','is_popular' => 'boolean'];
}
