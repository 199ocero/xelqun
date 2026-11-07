<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
 * @property int $sort
 * @property string $product
 * @property string $qty
 * @property string $price
 * @property string $subtotal
 * @property string|null $attachment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['order_id', 'sort', 'product', 'qty', 'price', 'subtotal', 'attachment'])]
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'qty' => 'float',
            'price' => 'float',
            'subtotal' => 'float',
        ];
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return HasMany<OrderItemPhoto, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(OrderItemPhoto::class);
    }

    /**
     * @return HasMany<OrderItemExtra, $this>
     */
    public function extras(): HasMany
    {
        return $this->hasMany(OrderItemExtra::class);
    }
}
