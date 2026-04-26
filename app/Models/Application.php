<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = [
        'job_id','application_type','applicant_name',
        'phone','normalized_phone','email','normalized_email',
        'status','applied_at',
        'is_valid','invalid_reason','is_billable','billable_snapshot','counted_at',
        'is_suspicious','suspicious_reason',
    ];

    protected $casts = [
        'applied_at'    => 'datetime',
        'counted_at'    => 'datetime',
        'is_valid'      => 'boolean',
        'is_billable'   => 'boolean',
        'is_suspicious' => 'boolean',
    ];

    const TYPE_LINE = 'line';
    const TYPE_FORM = 'form';

    const STATUS_RECEIVED  = 'received';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CLOSED    = 'closed';

    const INVALID_MISSING_FIELDS = 'missing_required_fields';
    const INVALID_DUPLICATE      = 'duplicate_application';
    const INVALID_SPAM           = 'spam_or_bot';
    const INVALID_TEST           = 'test_submission';
    const INVALID_MANUAL         = 'manually_invalidated';
    const INVALID_EMPLOYER       = 'employer_dispute';

    const INVALID_REASON_LABELS = [
        self::INVALID_MISSING_FIELDS => '必須項目不足',
        self::INVALID_DUPLICATE      => '重複応募',
        self::INVALID_SPAM           => 'スパム/ボット',
        self::INVALID_TEST           => 'テスト投稿',
        self::INVALID_MANUAL         => '手動無効',
        self::INVALID_EMPLOYER       => '掲載主申告',
    ];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function lineDetail(): HasOne { return $this->hasOne(LineApplicationDetail::class); }
    public function formDetail(): HasOne { return $this->hasOne(FormApplicationDetail::class); }
    public function formDesiredJobTypes(): HasMany { return $this->hasMany(FormDesiredJobType::class); }
    public function formDesiredConditions(): HasMany { return $this->hasMany(FormDesiredCondition::class); }
    public function lineConditionAnswers(): HasMany { return $this->hasMany(LineConditionAnswer::class); }
    public function billing(): HasOne { return $this->hasOne(Billing::class); }
    public function notifications(): HasMany { return $this->hasMany(ApplicationNotification::class); }
    public function disputes(): HasMany { return $this->hasMany(ApplicationDispute::class); }

    public function scopeLine($q) { return $q->where('application_type', self::TYPE_LINE); }
    public function scopeForm($q) { return $q->where('application_type', self::TYPE_FORM); }
    public function scopeValid($q) { return $q->where('is_valid', true); }
    public function scopeBillable($q) { return $q->where('is_billable', true); }

    public function isLine(): bool { return $this->application_type === self::TYPE_LINE; }
    public function isForm(): bool { return $this->application_type === self::TYPE_FORM; }
    public function isBilled(): bool { return $this->billing()->exists(); }

    public function invalidReasonLabel(): string
    {
        return self::INVALID_REASON_LABELS[$this->invalid_reason] ?? ($this->invalid_reason ?? '');
    }
}
