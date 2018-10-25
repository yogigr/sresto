<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DishCategory;
use Auth;

class DishCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', DishCategory::class);

        $dishCategories = DishCategory::all();
        return response()->json([
            'dishCategories' => $dishCategories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', DishCategory::class);

        $request->validate([
            'category_name' => 'required|string|unique:dish_categories,name',
            'category_description' => 'string'
        ]);

        $dishCategory = DishCategory::create([
            'name' => $request->category_name,
            'description' => $request->category_description,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'message' => __('Category Created'),
            'dishCategory' => $dishCategory
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DishCategory  $dishCategory
     * @return \Illuminate\Http\Response
     */
    public function show(DishCategory $dishCategory)
    {
        $this->authorize('view', DishCategory::class);

        return response()->json([
            'dishCategory' => $dishCategory
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DishCategory  $dishCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DishCategory $dishCategory)
    {
        $this->authorize('update', $dishCategory);

        $request->validate([
            'category_name' => 'required|string|unique:dish_categories,name,' . $dishCategory->id,
            'category_description' => 'string'
        ]);

        $dishCategory->name = $request->category_name;
        $dishCategory->description = $request->category_description;
        $dishCategory->save();
        
        return response()->json([
            'message' => __('Category Updated'),
            'dishCategory' => $dishCategory
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DishCategory  $dishCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(DishCategory $dishCategory)
    {
        $this->authorize('delete', $dishCategory);

        $dishCategory->delete();
        return response()->json([
            'message' => __('Category Deleted')
        ], 200);
    }
}
