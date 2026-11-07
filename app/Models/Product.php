<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * Seeded demo data. The public demo shows it, so
 * nothing a visitor types ever lands here.
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $sku
 * @property string $status
 * @property string $price
 * @property int $stock
 * @property bool $is_featured
 * @property Carbon|null $released_at
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['category_id', 'name', 'sku', 'status', 'price', 'stock', 'is_featured', 'released_at', 'image', 'brand_id', 'description', 'specs', 'keywords', 'gallery', 'color', 'discount', 'restock_at', 'pickup_time'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'released_at' => 'date',
            'specs' => 'array',
            'keywords' => 'array',
            'gallery' => 'array',
            'restock_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
