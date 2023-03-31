<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponProductRequest;
use App\Models\Coupon;
use Illuminate\Http\Response;

class CouponProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('seller');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponProductRequest $request): Response
    {
        $coupon = Coupon::find($request->coupon_id);

        $coupon->products()->sync($request->products);

        return response(['message' => __('coupon products has been updated successfully')], 201);
    }
}
