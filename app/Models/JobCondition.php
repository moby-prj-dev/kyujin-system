<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCondition extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'condition_id'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function condition(): BelongsTo { return $this->belongsTo(MasterCondition::class, 'condition_id'); }
}
