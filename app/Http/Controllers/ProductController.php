<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Seller;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('seller')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ProductFilter $filter): Response
    {
        return response(['data' => $filter->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): Response
    {
        return response([
            'data' => Product::create($request->validated()),
            'message' => __('product has been created successfully')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): Response
    {
        if (auth()?->user()->user_type == Seller::class) {
            abort_if($product->seller_id !== auth()->user()->user_id, 403);
        }

        return response(['data' => $product->load(['category'])]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): Response
    {
        $product->update($request->validated());

        return response([
            'data' => $product,
            'message' => __('product has been updated successfully')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): Response
    {
        abort_if($product->seller_id != auth()->user()->user_id, 403);

        $product->delete();

        return response(['message' => __('product has been deleted successfully')]);
    }
}
