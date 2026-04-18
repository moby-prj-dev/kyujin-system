<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDesiredCondition extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'condition_id'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function condition(): BelongsTo { return $this->belongsTo(MasterCondition::class, 'condition_id'); }
}
