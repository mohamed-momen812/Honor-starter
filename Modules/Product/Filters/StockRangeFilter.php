<?php

namespace Modules\Product\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class StockRangeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Validate the input
        if (!is_array($value) || count($value) !== 2) {
            return $query;
        }
        $min = $value[0];
        $max = $value[1];
        // Ensure min and max are numeric
        if (!is_numeric($min) || !is_numeric($max)) {
            return $query;
        }
        // Ensure min is less than or equal to max
        if ($min > $max) {
            return $query;
        }

        return $query->whereBetween('stock', [(int)$min, (int)$max]);
    }
}