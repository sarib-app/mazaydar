<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MealSelection extends Model {
    protected $fillable = ['subscription_id','week_start','day_short','day_id','selections'];
    protected $casts = ['selections' => 'array'];
}
