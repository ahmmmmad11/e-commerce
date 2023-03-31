<?php

namespace App\Observers;

use App\Models\Coupon;
use App\Models\Seller;

class CouponObserver
{
    /**
     * Handle the Coupon "creating" event.
     */
    public function creating(Coupon $coupon): void
    {
        $user = auth()->user();

        if ($user->user_type == Seller::class) {
            $coupon->seller_id  = $user->user_id;
        }
    }
}
