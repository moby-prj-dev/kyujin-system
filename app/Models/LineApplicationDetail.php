<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineApplicationDetail extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id','line_user_id','line_session_id','available_from','experience_flag','preferred_conditions_summary','area','appeal_message','screener_answers','raw_answers_json'];
    protected $casts = ['experience_flag' => 'boolean', 'raw_answers_json' => 'array', 'screener_answers' => 'array'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
}
