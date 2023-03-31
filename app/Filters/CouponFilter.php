<?php

namespace App\Filters;

use App\Models\Coupon;
use App\Models\Seller;
use Spatie\QueryBuilder\QueryBuilder;

class CouponFilter extends Filter
{
    public function filter(): self
    {
        $builder = QueryBuilder::for(Coupon::class)
            ->allowedFilters([
                'id', 'seller_id', 'coupon', 'type', 'amount', 'end_at', 'status', 'created_at', 'updated_at'
            ])
            ->allowedIncludes([
                'products', 'seller'
            ])
            ->allowedSorts([
                'amount', 'status', 'created_at', 'type'
            ]);

        if (auth()->user()->user_type === Seller::class) {
            $builder->where('seller_id', auth()->user()->user_id);
        }

        $this->data = $builder;

        return $this;
    }
}
