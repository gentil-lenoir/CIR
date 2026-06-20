<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory;

    public const ROLE_SYSTEM_MANAGER = 'system_manager';

    public const ROLE_MUNICIPAL_MANAGER = 'municipal_manager';

    public const ROLE_WORKER = 'worker';

    public const ROLE_CITIZEN = 'citizen';

    public const ROLES = [
        self::ROLE_SYSTEM_MANAGER,
        self::ROLE_MUNICIPAL_MANAGER,
        self::ROLE_WORKER,
        self::ROLE_CITIZEN,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'preferred_location',
        'password',
        'role',
        'theme_preference',
        'text_size',
        'availability_status',
        'preferred_zone',
        'shift_window',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            if (! in_array($user->role, self::ROLES, true)) {
                throw ValidationException::withMessages([
                    'role' => 'Invalid role selected.',
                ]);
            }

            if ($user->role !== self::ROLE_MUNICIPAL_MANAGER) {
                return;
            }

            $query = self::query()
                ->where('role', self::ROLE_MUNICIPAL_MANAGER)
                ->when($user->exists, fn ($builder) => $builder->whereKeyNot($user->getKey()));

            if ($query->exists()) {
                throw ValidationException::withMessages([
                    'role' => 'Only one municipal manager can exist in the system.',
                ]);
            }
        });
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(Upvote::class);
    }

    public function assignedIssues(): HasMany
    {
        return $this->hasMany(IssueAssignment::class, 'worker_id');
    }

    public function escalatedIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'escalated_to');
    }
}
