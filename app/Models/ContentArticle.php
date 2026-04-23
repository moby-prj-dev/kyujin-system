<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentArticle extends Model
{
    protected $fillable = [
        'slug', 'category', 'title', 'h1', 'meta_description', 'body',
        'image_url', 'area_id', 'job_type_id', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(MasterArea::class, 'area_id');
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(MasterJobType::class, 'job_type_id');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
