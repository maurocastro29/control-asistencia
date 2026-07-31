<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'username',
        'first_name',
        'middle_name',
        'first_last_name',
        'second_last_name',
        'password',
        'last_login_at',
        'is_active',
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
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(
                implode(' ', array_filter([
                    $this->first_name,
                    $this->middle_name,
                    $this->first_last_name,
                    $this->second_last_name,
                ]))
            ),
        );
    }

    protected function shortName(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->first_name} {$this->first_last_name}",
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}