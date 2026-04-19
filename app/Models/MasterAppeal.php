<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterAppeal extends Model
{
    public $timestamps = false;
    protected $fillable = ['category', 'name', 'question_text', 'slug', 'sort_order', 'is_active', 'score', 'is_main_candidate'];
    protected $casts = ['is_active' => 'boolean', 'is_main_candidate' => 'boolean'];

    public function jobAppeals(): HasMany { return $this->hasMany(JobAppeal::class, 'appeal_id'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeByCategory($q, string $cat) { return $q->where('category', $cat); }
    public function scopeHasQuestion($q) { return $q->whereNotNull('question_text'); }
}
