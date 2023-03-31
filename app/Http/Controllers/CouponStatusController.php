<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCouponStatusRequest;
use App\Models\Coupon;
use Illuminate\Http\Response;

class CouponStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('seller');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponStatusRequest $request, Coupon $coupon): Response
    {
        $coupon->update(['status' => $request->status]);

        return response([
            'data' => $coupon,
            'message' => __('coupon status has been updated successfully')
        ]);
    }
}
