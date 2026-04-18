<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingAgreement extends Model
{
    public $timestamps = false;
    protected $fillable = ['job_id','agreement_flag','agreement_text','agreement_text_version','agreed_at','agreed_ip','user_agent'];
    protected $casts = ['agreement_flag' => 'boolean', 'agreed_at' => 'datetime'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }
}
