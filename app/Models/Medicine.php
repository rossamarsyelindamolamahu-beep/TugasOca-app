<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['category_id', 'name', 'stock', 'unit', 'price', 'expiration_date', 'description'])]
class Medicine extends Model
{
    /**
     * Batas minimum stok. Jika stok <= batas ini maka status "Stok Menipis".
     */
    public const int LOW_STOCK_THRESHOLD = 10;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'price' => 'integer',
            'expiration_date' => 'date',
        ];
    }

    /**
     * Get the category this medicine belongs to.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the stock movement history for this medicine.
     *
     * @return HasMany<StockMovement, $this>
     */
    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    /**
     * Whether the stock is at or below the minimum threshold.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= self::LOW_STOCK_THRESHOLD;
    }

    /**
     * Human readable stock status.
     */
    public function stockStatus(): string
    {
        return $this->isLowStock() ? 'Stok Menipis' : 'Stok Aman';
    }
}
