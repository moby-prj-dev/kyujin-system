<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormApplicationDetail extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'desired_area_id', 'appeal_message', 'screener_answers', 'ip_address', 'user_agent'];
    protected $casts = ['screener_answers' => 'array'];

    public function desiredArea(): BelongsTo { return $this->belongsTo(MasterArea::class, 'desired_area_id'); }

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
}
