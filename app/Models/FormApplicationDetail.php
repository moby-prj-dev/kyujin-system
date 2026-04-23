<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormApplicationDetail extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'desired_area_id', 'appeal_message', 'ip_address', 'user_agent'];

    public function desiredArea(): BelongsTo { return $this->belongsTo(MasterArea::class, 'desired_area_id'); }

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
}
