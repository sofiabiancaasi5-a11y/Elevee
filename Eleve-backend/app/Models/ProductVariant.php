<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'color_hex',
        'sku',
        'stock_quantity',
        'price_adjustment',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'price_adjustment' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->product ? $this->product->base_price + $this->price_adjustment : 0.00);
    }
}
