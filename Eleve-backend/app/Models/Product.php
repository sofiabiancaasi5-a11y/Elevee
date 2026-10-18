<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'base_price',
        'primary_image',
        'images',
        'status',
        'is_featured',
        'is_bestseller',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants->sum('stock_quantity');
    }

    public function getAvailableColorsAttribute(): array
    {
        return $this->variants
            ->unique('color')
            ->map(fn($v) => [
                'name' => $v->color,
                'hex' => $v->color_hex,
            ])
            ->values()
            ->all();
    }

    public function getAvailableSizesAttribute(): array
    {
        return $this->variants
            ->pluck('size')
            ->unique()
            ->values()
            ->all();
    }
}
