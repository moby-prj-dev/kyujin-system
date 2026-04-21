<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Job extends Model
{
    use SoftDeletes;
    protected $table = 'job_listings';
    protected $fillable = ['company_name','title','seo_title','subtitle','lp_tags','meta_description','description_generated','free_text','photo_path','status','token','contact_email','contact_phone','expires_at','paused_at','email_verification_token','email_verified_at','trial_warning_sent_at','expired_notified_at','continued_at','continue_notified_at'];

    protected $casts = [
        'expires_at'            => 'datetime',
        'paused_at'             => 'datetime',
        'email_verified_at'     => 'datetime',
        'continued_at'          => 'datetime',
        'continue_notified_at'  => 'datetime',
        'lp_tags'               => 'array',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_DRAFT   = 'draft';
    const STATUS_ACTIVE  = 'active';
    const STATUS_PAUSED  = 'paused';
    const STATUS_CLOSED  = 'closed';
    const STATUS_EXPIRED = 'expired';

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Job $job) {
            if (empty($job->token)) { $job->token = Str::random(32); }
        });
    }

    public function jobAreas(): HasMany { return $this->hasMany(JobArea::class); }
    public function jobJobTypes(): HasMany { return $this->hasMany(JobJobType::class); }
    public function jobEmploymentTypes(): HasMany { return $this->hasMany(JobEmploymentType::class); }
    public function jobConditions(): HasMany { return $this->hasMany(JobCondition::class); }
    public function jobAppeals(): HasMany { return $this->hasMany(JobAppeal::class); }
    public function billingAgreement(): HasOne { return $this->hasOne(BillingAgreement::class); }
    public function applications(): HasMany { return $this->hasMany(Application::class); }
    public function billings(): HasMany { return $this->hasMany(Billing::class); }
    public function emailVerificationTokens(): HasMany { return $this->hasMany(EmailVerificationToken::class); }
    public function lineEntryTokens(): HasMany { return $this->hasMany(LineEntryToken::class); }

    public function scopeActive($q) { return $q->where('status', self::STATUS_ACTIVE); }
    public function scopeByToken($q, string $token) { return $q->where('token', $token); }
    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }
    public function hasValidAgreement(): bool { return $this->billingAgreement()->exists(); }
}
