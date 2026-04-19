<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterJobType extends Model
{
    public $timestamps = false;
    protected $fillable = ['category', 'name', 'slug', 'sort_order', 'is_active', 'score', 'is_main_candidate'];
    protected $casts = ['is_active' => 'boolean', 'is_main_candidate' => 'boolean'];

    public function jobs(): HasMany { return $this->hasMany(Job::class, 'job_type_id'); }
    public function formDesiredJobTypes(): HasMany { return $this->hasMany(FormDesiredJobType::class, 'job_type_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByCategory($q, string $cat) { return $q->where('category', $cat); }
}
