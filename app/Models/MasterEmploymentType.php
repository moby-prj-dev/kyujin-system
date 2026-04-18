<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterEmploymentType extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'slug', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function jobs(): HasMany { return $this->hasMany(Job::class, 'employment_type_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
