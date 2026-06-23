<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class AdminUser extends Model {
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id','username','password_hash','role'];
    protected $hidden = ['password_hash'];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->id = $m->id ?: Str::uuid());
    }
}
