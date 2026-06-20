<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class IssueAssignment extends Model
{
    protected $fillable = [
        'issue_id',
        'worker_id',
        'assigned_by',
        'status',
        'assigned_at',
        'deadline_at',
        'notes',
        'proof_path',
        'proof_url',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'deadline_at' => 'datetime',
        ];
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
