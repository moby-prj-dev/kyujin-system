<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationNotification extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'sent_to', 'send_status', 'error_message', 'sent_at'];
    protected $casts = ['sent_at' => 'datetime'];

    const STATUS_SENT   = 'sent';
    const STATUS_FAILED = 'failed';

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function scopeFailed($q) { return $q->where('send_status', self::STATUS_FAILED); }
}
