<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'user_id',
        'worker_id',
        'escalated_to',
        'escalated_at',
        'citizen_feedback',
        'feedback_submitted_at',
        'title',
        'description',
        'category',
        'status',
        'deadline',
        'priority',
        'latitude',
        'longitude',
        'address',
        'upvotes_count',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'reported_at' => 'datetime',
            'escalated_at' => 'datetime',
            'feedback_submitted_at' => 'datetime',
            'deadline' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(IssueImage::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(Upvote::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(IssueStatusHistory::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(IssueAssignment::class);
    }

    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }
}
