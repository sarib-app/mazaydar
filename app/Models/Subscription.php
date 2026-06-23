<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Subscription extends Model {
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id','user_id','package_id','status','start_date','end_date','pause_start','resume_date','pauses_used','payment_method','address_id'];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->id = $m->id ?: Str::uuid());
    }
}
