<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobJobType extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'job_type_id'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function jobType(): BelongsTo { return $this->belongsTo(MasterJobType::class, 'job_type_id'); }
}
