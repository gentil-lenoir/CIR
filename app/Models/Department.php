<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function issues(): HasManyThrough
    {
        return $this->hasManyThrough(Issue::class, Worker::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(IssueAssignment::class);
    }

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }
}
