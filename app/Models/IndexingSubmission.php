<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndexingSubmission extends Model
{
    protected $fillable = [
        'url',
        'type',
        'action',
        'http_status',
        'response_body',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'http_status'  => 'integer',
    ];
}
