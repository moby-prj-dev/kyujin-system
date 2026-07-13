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
    protected $fillable = ['source','hw_job_no','hw_job_url','company_name','title','seo_title','subtitle','lp_tags','meta_description','description_generated','free_text','salary_type','salary_min','salary_max','salary_note','photo_path','status','is_admin_hidden','is_monitor','is_permanently_free','plan','plan_started_at','monitor_ends_at','admin_memo','admin_memo_updated_at','token','contact_email','secondary_emails','contact_phone','expires_at','paused_at','email_verification_token','email_verified_at','trial_warning_sent_at','expired_notified_at','continued_at','continue_notified_at'];

    const SALARY_TYPES = [
        'monthly' => '月給',
        'hourly'  => '時給',
        'daily'   => '日給',
        'yearly'  => '年収',
        'other'   => 'その他',
    ];

    protected $casts = [
        'expires_at'            => 'datetime',
        'paused_at'             => 'datetime',
        'email_verified_at'     => 'datetime',
        'continued_at'          => 'datetime',
        'continue_notified_at'  => 'datetime',
        'plan_started_at'       => 'datetime',
        'lp_tags'               => 'array',
        'secondary_emails'      => 'array',
        'is_monitor'            => 'boolean',
        'is_permanently_free'   => 'boolean',
        'monitor_ends_at'       => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_DRAFT   = 'draft';
    const STATUS_ACTIVE  = 'active';
    const STATUS_PAUSED  = 'paused';
    const STATUS_CLOSED  = 'closed';
    const STATUS_EXPIRED = 'expired';

    const PLAN_BASIC    = 'basic';
    const PLAN_STANDARD = 'standard';
    const STANDARD_MAX_JOBS = 3;

    public function isStandard(): bool { return $this->plan === self::PLAN_STANDARD; }
    public function notificationEmails(): array
    {
        $primary = $this->contact_email ? [$this->contact_email] : [];
        $extras  = is_array($this->secondary_emails) ? $this->secondary_emails : [];
        return array_values(array_unique(array_filter(array_merge($primary, $extras))));
    }

    /**
     * スタンダード月額請求の初回請求日(翌月1日起算)
     * 案A: 翌月起算ルール - plan_started_at の翌月1日から月額料金が発生
     */
    public function nextBillingDate(): ?\Carbon\Carbon
    {
        if (!$this->isStandard() || !$this->plan_started_at) {
            return null;
        }
        return $this->plan_started_at->copy()->addMonthNoOverflow()->startOfMonth();
    }

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

    public function salaryText(): string
    {
        if (!$this->salary_type || !$this->salary_min) return '';

        $label = self::SALARY_TYPES[$this->salary_type] ?? 'その他';
        $min   = number_format($this->salary_min) . '円';

        if ($this->salary_max) {
            return $label . ' ' . $min . '〜' . number_format($this->salary_max) . '円';
        }

        return $label . ' ' . $min . '〜';
    }

    public function scopeActive($q) { return $q->where('status', self::STATUS_ACTIVE); }
    public function scopeByToken($q, string $token) { return $q->where('token', $token); }
    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }
    public function hasValidAgreement(): bool { return $this->billingAgreement()->exists(); }
}
