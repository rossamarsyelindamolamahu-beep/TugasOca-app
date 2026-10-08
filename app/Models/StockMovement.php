<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['medicine_id', 'type', 'quantity', 'description'])]
class StockMovement extends Model
{
    public const string TYPE_IN = 'in';

    public const string TYPE_OUT = 'out';

    /**
     * Get the medicine this movement belongs to.
     *
     * @return BelongsTo<Medicine, $this>
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Whether this movement adds stock.
     */
    public function isIncoming(): bool
    {
        return $this->type === self::TYPE_IN;
    }

    /**
     * Human readable movement type.
     */
    public function typeLabel(): string
    {
        return $this->isIncoming() ? 'Masuk' : 'Keluar';
    }
}
