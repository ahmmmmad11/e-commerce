<?php

namespace App\Http\Controllers;

use App\Http\Requests\VariantRequest;
use App\Models\Product;
use Illuminate\Http\Response;

class VariantController extends Controller
{
    public function __construct()
    {
        $this->middleware('seller');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VariantRequest $request): Response
    {
        $product = Product::find($request->id);

        $product->variants()->sync($request->variants);

        return response([
            'data' => $product->variants,
            'message' => __('variants has been created successfully')
        ]);
    }
}
