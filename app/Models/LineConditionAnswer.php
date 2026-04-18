<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineConditionAnswer extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'condition_id', 'answer', 'answer_text'];

    const ANSWER_YES     = 'yes';
    const ANSWER_CONSULT = 'consult';
    const ANSWER_OTHER   = 'other';

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function condition(): BelongsTo { return $this->belongsTo(MasterCondition::class, 'condition_id'); }
    public function isAgreed(): bool { return $this->answer === self::ANSWER_YES; }
}
