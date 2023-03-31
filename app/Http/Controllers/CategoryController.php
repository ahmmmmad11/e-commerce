<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('seller');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response(['data' => Category::all()]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): Response
    {
        return response(['data' => $category->load(['parent', 'children'])]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): Response
    {
        $category->delete();

        return response(['message' => __('category has been deleted')]);
    }
}
