<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class PackageWeeklyMenu extends Model {
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id','package_id','week_start','day_short','day_full','meals'];
    protected $casts = ['meals' => 'array'];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->id = $m->id ?: Str::uuid());
    }
}
