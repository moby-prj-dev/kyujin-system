<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $fillable   = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::find($key)?->value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }

    public static function monitorStartDate(): string
    {
        return static::get('monitor_start_date', '2026-04-28');
    }

    public static function monitorMonths(): int
    {
        return (int) static::get('monitor_months', 3);
    }

    public static function monitorCutoffDate(): Carbon
    {
        // 手動解除フラグが立ってる時は過去日付を返して全モニターUIを非表示化
        if (static::isMonitorDisabled()) {
            return Carbon::parse('2000-01-01');
        }
        return Carbon::parse(static::monitorStartDate())->addMonths(static::monitorMonths());
    }

    public static function isMonitorDisabled(): bool
    {
        return (bool) static::get('monitor_disabled', 0);
    }
}
