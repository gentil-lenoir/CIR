<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Worker extends Authenticatable
{
    use HasApiTokens, HasFactory;

    public const ROLE = 'worker';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'department_id',
        'password',
        'status',
        'availability_status',
        'theme_preference',
        'preferred_zone',
        'shift_window',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'role',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getRoleAttribute(): string
    {
        return self::ROLE;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }
}
