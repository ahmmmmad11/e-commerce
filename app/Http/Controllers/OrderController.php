<?php

namespace App\Http\Controllers;

use App\Filters\OrderFilter;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderServices;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('customer')->only(['store', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(OrderFilter $filter): Response
    {
        return response(['data' => $filter->get()]);
    }

    /**
     * Store a newly created resource in storage.
     * @throws ValidationException
     */
    public function store(StoreOrderRequest $request): Response
    {
        $order = OrderServices::createOrder($request);

        return response([
            'data' => $order->load(['products']),
            'message' => __('the order has been created successfully')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): Response
    {
        abort_if($order->customer_id !== auth()->user()->user_id, 403);

        return response(['data' => $order]);
    }
}
