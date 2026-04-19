<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobEmploymentType extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'employment_type_id'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function employmentType(): BelongsTo { return $this->belongsTo(MasterEmploymentType::class, 'employment_type_id'); }
}
