<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = ['job_id','application_type','applicant_name','phone','email','status','applied_at'];
    protected $casts = ['applied_at' => 'datetime'];

    const TYPE_LINE = 'line';
    const TYPE_FORM = 'form';
    const STATUS_RECEIVED  = 'received';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CLOSED    = 'closed';

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
    public function lineDetail(): HasOne { return $this->hasOne(LineApplicationDetail::class); }
    public function formDetail(): HasOne { return $this->hasOne(FormApplicationDetail::class); }
    public function formDesiredJobTypes(): HasMany { return $this->hasMany(FormDesiredJobType::class); }
    public function formDesiredConditions(): HasMany { return $this->hasMany(FormDesiredCondition::class); }
    public function lineConditionAnswers(): HasMany { return $this->hasMany(LineConditionAnswer::class); }
    public function billing(): HasOne { return $this->hasOne(Billing::class); }
    public function notifications(): HasMany { return $this->hasMany(ApplicationNotification::class); }

    public function scopeLine($q) { return $q->where('application_type', self::TYPE_LINE); }
    public function scopeForm($q) { return $q->where('application_type', self::TYPE_FORM); }
    public function isLine(): bool { return $this->application_type === self::TYPE_LINE; }
    public function isForm(): bool { return $this->application_type === self::TYPE_FORM; }
    public function isBilled(): bool { return $this->billing()->exists(); }
}
