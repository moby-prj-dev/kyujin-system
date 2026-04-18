<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmailVerificationToken extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id', 'token', 'email', 'expires_at', 'used_at'];
    protected $casts = ['expires_at' => 'datetime', 'used_at' => 'datetime'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (EmailVerificationToken $r) {
            if (empty($r->token)) { $r->token = Str::random(64); }
            if (empty($r->expires_at)) { $r->expires_at = now()->addMinutes(30); }
        });
    }

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function isValid(): bool { return is_null($this->used_at) && $this->expires_at->isFuture(); }
    public function markAsUsed(): void { $this->update(['used_at' => now()]); }
}
