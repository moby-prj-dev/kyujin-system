<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingSummary extends Model
{
    protected $fillable = [
        'contact_email','billing_month',
        'valid_count','billable_count',
        'unit_price','total_amount',
        'status','sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    const STATUS_UNBILLED  = 'unbilled';
    const STATUS_SENT      = 'sent';
    const STATUS_PAID      = 'paid';
    const STATUS_ON_HOLD   = 'on_hold';
    const STATUS_UNPAID    = 'unpaid';
    const STATUS_OVERDUE   = 'overdue';

    const STATUS_LABELS = [
        self::STATUS_UNBILLED => '未請求',
        self::STATUS_SENT     => '送付済',
        self::STATUS_PAID     => '入金済',
        self::STATUS_ON_HOLD  => '保留',
        self::STATUS_UNPAID   => '未払い',
        self::STATUS_OVERDUE  => '期限超過',
    ];

    const STATUS_BADGE = [
        self::STATUS_UNBILLED => 'bg-warning text-dark',
        self::STATUS_SENT     => 'bg-primary',
        self::STATUS_PAID     => 'bg-success',
        self::STATUS_ON_HOLD  => 'bg-secondary',
        self::STATUS_UNPAID   => 'bg-danger',
        self::STATUS_OVERDUE  => 'bg-danger',
    ];

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function statusBadge(): string
    {
        return self::STATUS_BADGE[$this->status] ?? 'bg-secondary';
    }
}
