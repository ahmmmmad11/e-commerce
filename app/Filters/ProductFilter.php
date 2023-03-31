<?php

namespace App\Filters;

use App\Filters\CustomFilters\FilterByRate;
use App\Filters\CustomFilters\FilterByStock;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Seller;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductFilter extends Filter
{
    public function filter(): self
    {
        $builder = QueryBuilder::for(Product::class)
            ->allowedFilters([
                'id', 'seller_id', 'category_id', 'name', 'image', 'price',
                'quantity', 'options', 'status', 'created_at', 'updated_at',
                'category.name', 'seller.name',
                AllowedFilter::custom('rating', new FilterByRate()),
                AllowedFilter::custom('in_stock', new FilterByStock())->default(true),
            ])
            ->allowedIncludes([
                'seller', 'variants', 'category', 'ratings'
            ])
            ->allowedSorts([
                'category_id', 'price', 'quantity', 'created_at', 'status'
            ]);

        if (auth()->user()->user_type === Seller::class) {
            $builder->where('seller_id', auth()->user()->user_id);
        }

        if (auth()->user()->user_type === Customer::class) {
            $builder->where('status', '!=' , 'disabled');
        }

        $this->data = $builder;

        return $this;
    }
}
