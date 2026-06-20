<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class IssueImage extends Model
{
    protected $fillable = [
        'issue_id',
        'image_path',
        'image_url',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }
}
