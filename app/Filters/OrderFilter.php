<?php

namespace App\Filters;

use App\Filters\CustomIncludes\IncludeOrderProducts;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Seller;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

class OrderFilter extends Filter
{
    public function filter(): self
    {
        $builder = QueryBuilder::for(Order::class);

        // seller cannot view order amount, total and delivery amount
        if (auth()->user()->user_type == Seller::class) {
            $builder->select(['id', 'customer_id', 'status', 'created_at', 'updated_at']);

            if (! request('include')) {
                $builder->whereHas('products', function ($products) {
                    return $products->whereHas('product', fn($product) => $product
                        ->where('seller_id', auth()->user()->user_id));
                });
            }
        }

        $builder->allowedFilters([
                'id', 'customer_id', 'amount', 'delivery_amount', 'total', 'address', 'delivery_provider', 'status', 'created_at', 'updated_at'
            ]);

        $builder->allowedIncludes([
                'customer', AllowedInclude::custom('products', new IncludeOrderProducts())
            ])
            ->allowedSorts([
                'customer_id', 'amount', 'delivery_amount', 'total',  'created_at', 'status'
            ]);

        if (auth()->user()->user_type === Customer::class) {
            $builder->where('customer_id', auth()->user()->user_id);
        }

        $this->data = $builder;

        return $this;
    }
}
