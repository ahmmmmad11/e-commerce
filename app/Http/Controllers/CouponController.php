<?php

namespace App\Http\Controllers;

use App\Filters\CouponFilter;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Models\Customer;
use Illuminate\Http\Response;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('seller');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CouponFilter $filter): Response
    {
        abort_if(auth()->user()->user_type ===Customer::class, 403);

        return response([
            'data' => $filter->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponRequest $request): Response
    {
        return response([
            'data' => Coupon::create($request->validated()),
            'message' => __('coupon has been created successfully')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon): Response
    {
        abort_if($coupon->seller_id !== auth()->user()->user_id, 403);

        return response([
            'data' => $coupon->load('products')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponRequest $request, Coupon $coupon): Response
    {
        abort_if($coupon->seller_id !== auth()->user()->user_id, 403);

        $coupon->update($request->validated());

        return response([
            'data' => $coupon,
            'message' => __('coupon has been updated successfully')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon): Response
    {
        abort_if($coupon->seller_id !== auth()->user()->user_id, 403);

        $coupon->delete();

        return response(['message' => __('Coupon has been deleted successfully')]);
    }
}
