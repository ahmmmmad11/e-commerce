<?php

namespace App\Filters\CustomFilters;

use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterByRate implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas('ratings', function (Builder $query) use ($value) {
             return $query->selectRaw('product_id, stars')
                 ->groupBy('product_id')
                 ->havingRaw('AVG(stars) >= ?', [(int) $value]);
        });

    }
}
