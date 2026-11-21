<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    public const UPDATED_AT = null;

    protected $fillable = ['username', 'full_name', 'password', 'avatar'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            // Automatically bcrypt-hashes the password whenever it is assigned.
            'password' => 'hashed',
        ];
    }

    /** The schema has no remember_token column, so "remember me" is disabled. */
    public function getRememberTokenName(): string
    {
        return '';
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'sold_by');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    public function getInitialsAttribute(): string
    {
        return initials($this->full_name);
    }
}
