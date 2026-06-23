<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Address extends Model {
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id','user_id','label','street','building','floor','apartment','instructions','is_default','lat','lng'];
    protected $casts = ['is_default' => 'boolean'];
    protected static function boot() {
        parent::boot();
        static::creating(fn($m) => $m->id = $m->id ?: Str::uuid());
    }
}
