<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id','application_id','application_type','billing_trigger_type','amount','currency','billed_at','agreement_version','billing_status','notification_status'];
    protected $casts = ['billed_at' => 'datetime', 'amount' => 'integer'];

    const BILLING_STATUS_PENDING = 'pending';
    const BILLING_STATUS_BILLED  = 'billed';
    const BILLING_STATUS_PAID    = 'paid';
    const NOTIFICATION_STATUS_UNSENT = 'unsent';
    const NOTIFICATION_STATUS_SENT   = 'sent';
    const NOTIFICATION_STATUS_FAILED = 'failed';

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function scopePending($q) { return $q->where('billing_status', self::BILLING_STATUS_PENDING); }
}
