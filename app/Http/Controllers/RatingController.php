<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('customer')->except('index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response(['data' => Rating::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRatingRequest $request): Response
    {
        return response([
            'data' => Rating::create($request->validated()),
            'message' => __('rating has been created successfully')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating): Response
    {
        $rating->delete();

        return response(['message' => __('rating has been deleted')]);
    }
}
