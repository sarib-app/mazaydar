<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class User extends Model {
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id','phone','name','email','age','gender','height','weight','goal','preferences'];
    protected $casts = ['preferences' => 'array'];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->id = $m->id ?: Str::uuid());
    }
}
