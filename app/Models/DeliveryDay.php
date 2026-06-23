<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeliveryDay extends Model {
    protected $fillable = ['subscription_id','days'];
    protected $casts = ['days' => 'array'];
}
