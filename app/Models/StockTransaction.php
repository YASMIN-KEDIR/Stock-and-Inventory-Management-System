<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTypeAttribute(): string
    {
        return match ($this->type) {
            'PURCHASE' => 'Purchase Stock-In',
            'SALE' => 'Sales Stock-Out',
            'ADJUSTMENT_ADD' => 'Manual Adjustment (+)',
            'ADJUSTMENT_SUB' => 'Manual Adjustment (-)',
            'CUSTOMER_RETURN' => 'Customer Return',
            'SUPPLIER_RETURN' => 'Supplier Return',
            default => $this->type,
        };
    }
}
