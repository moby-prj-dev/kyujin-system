<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDispute extends Model
{
    protected $fillable = [
        'application_id', 'job_token', 'reason', 'status', 'admin_note', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const MONTHLY_LIMIT = 3;

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public static function monthlyCountForJob(string $contactEmail): int
    {
        return static::whereHas('application.job', fn($q) => $q->where('contact_email', $contactEmail))
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }
}
