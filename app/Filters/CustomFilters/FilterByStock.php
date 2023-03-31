<?php

namespace App\Filters\CustomFilters;

use App\Models\Customer;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterByStock implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        if ($value && auth()->user()->user_type == Customer::class)
        {
            $query->where('quantity', '>', 0);
        }
    }
}
