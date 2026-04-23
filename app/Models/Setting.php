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
        return static::get('monitor_start_date', now()->toDateString());
    }

    public static function monitorMonths(): int
    {
        return (int) static::get('monitor_months', 3);
    }

    public static function monitorCutoffDate(): Carbon
    {
        return Carbon::parse(static::monitorStartDate())->addMonths(static::monitorMonths());
    }
}
