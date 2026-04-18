<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterArea extends Model
{
    public $timestamps = false;
    protected $fillable = ['prefecture', 'region', 'name', 'slug', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function jobs(): HasMany { return $this->hasMany(Job::class, 'area_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByPrefecture($q, string $prefecture) { return $q->where('prefecture', $prefecture); }
}
