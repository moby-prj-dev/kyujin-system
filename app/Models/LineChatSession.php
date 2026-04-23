<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineChatSession extends Model
{
    protected $fillable = [
        'line_user_id', 'step', 'job_id', 'entry_token',
        'search_conditions_json', 'applicant_name', 'expires_at',
    ];

    protected $casts = [
        'search_conditions_json' => 'array',
        'expires_at'             => 'datetime',
    ];

    const STEP_CONFIRMING       = 'confirming';
    const STEP_COLLECTING_NAME  = 'collecting_name';
    const STEP_COLLECTING_PHONE = 'collecting_phone';

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public static function findActive(string $lineUserId): ?self
    {
        return static::where('line_user_id', $lineUserId)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }
}
