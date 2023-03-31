<?php

namespace App\Filters\CustomIncludes;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Includes\IncludeInterface;

class IncludeOrderProducts implements IncludeInterface
{
    public function __invoke(Builder $query, string $relations)
    {
        if (auth()->user()->user_type === Seller::class) {
            $query->withWhereHas('products', function ($products) {
                return $products->withWhereHas('product', fn($product) => $product
                    ->where('seller_id', auth()->user()->user_id)
                    ->select(['name', 'image', 'id']));
            });
        } else {
            $query->with('products.product:id,name,image');
        }
    }
}
