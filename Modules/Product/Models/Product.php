<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'sku',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function scopeWherePriceLessThanOrEqual($query, $value)
    {
        return $query->where('price', '<=', $value);
    }

    public function scopeWherePriceGreaterThanOrEqual($query, $value)
    {
        return $query->where('price', '>=', $value);
    }
}
