<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobArea extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'area_id'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function area(): BelongsTo { return $this->belongsTo(MasterArea::class, 'area_id'); }
}
