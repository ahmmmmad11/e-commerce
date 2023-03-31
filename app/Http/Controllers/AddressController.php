<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use Illuminate\Http\Response;

class AddressController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(AddressRequest $request): Response
    {
        $address = auth()->user()->user->address()->create($request->validated());

        return response([
            'data' => $address,
            'message' => __('the address added successfully')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddressRequest $request, Address $address): Response
    {
        $address->update($request->validated());

        return response([
            'data' => $address,
            'message' => __('the address updated successfully')
        ]);
    }
}
