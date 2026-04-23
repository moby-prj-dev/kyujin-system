<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDesiredAppeal extends Model
{
    public $timestamps = false;
    protected $fillable = ['application_id', 'appeal_id'];

    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function appeal(): BelongsTo { return $this->belongsTo(MasterAppeal::class, 'appeal_id'); }
}
