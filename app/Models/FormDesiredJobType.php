<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDesiredJobType extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'job_type_id'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function jobType(): BelongsTo { return $this->belongsTo(MasterJobType::class, 'job_type_id'); }
}
