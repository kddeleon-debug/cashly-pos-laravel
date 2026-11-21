<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const UPDATED_AT = null; // the schema only has created_at

    public const LOW_STOCK_THRESHOLD = 5;

    protected $fillable = ['name', 'price', 'stock_quantity', 'image'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    public function getStockStatusAttribute(): string
    {
        return match (true) {
            $this->stock_quantity <= 0 => 'out',
            $this->stock_quantity <= self::LOW_STOCK_THRESHOLD => 'low',
            default => 'ok',
        };
    }
}
