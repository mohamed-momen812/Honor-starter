<?php

namespace Modules\Product\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class StockRangeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        [$min, $max] = explode(',', (string)$value);
        return $query->whereBetween('stock', [(int)$min, (int)$max]);
    }
}
