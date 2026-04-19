<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LpView extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'ip_address', 'user_agent', 'viewed_at'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
}
