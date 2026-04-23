<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDesiredEmploymentType extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'employment_type_id'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function employmentType(): BelongsTo { return $this->belongsTo(MasterEmploymentType::class, 'employment_type_id'); }
}
