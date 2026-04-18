<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAppeal extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'appeal_id'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function appeal(): BelongsTo { return $this->belongsTo(MasterAppeal::class, 'appeal_id'); }
}
