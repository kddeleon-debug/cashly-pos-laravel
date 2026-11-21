<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['full_name', 'email', 'phone', 'avatar'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
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
