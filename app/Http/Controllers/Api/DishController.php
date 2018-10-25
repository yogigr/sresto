<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\CodeGenerator;
use App\Traits\FileUploader;
use App\Dish;
use Auth;

class DishController extends Controller
{
    use CodeGenerator, FileUploader;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Dish::class);

        $dishes = Dish::all();
        return response()->json([
            'dishes' => $dishes
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
        $this->authorize('create', Dish::class);

        $request->validate([
            'dish_name' => 'required|string|max:25|unique:dishes,name',
            'dish_description' => 'required|string',
            'dish_category_id' => 'required|numeric',
            'dish_price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'dish_image' => 'image|mimes:jpeg|max:200'
        ]);

        $dish = Dish::create([
            'code' => $this->getCode('DS', 'dishes'),
            'name' => $request->dish_name,
            'description' => $request->dish_description,
            'dish_category_id' => $request->dish_category_id,
            'price' => $request->dish_price,
            'user_id' => Auth::id()
        ]);

        if ($request->hasFile('dish_image')) {
            $filename = $this->uploadImage($request, 'dish_image', $dish->name, 'images/dishes/');
            $dish->image = $filename;
            $dish->save();
        }

        return response()->json([
            'message' => __('Dish Created'),
            'dish' => $dish
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function show(Dish $dish)
    {
        $this->authorize('view', $dish);

        return response()->json([
            'dish' => $dish
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dish $dish)
    {
        $this->authorize('update', $dish);

        $request->validate([
            'dish_name' => 'required|string|max:25|unique:dishes,name,' . $dish->id,
            'dish_description' => 'required|string',
            'dish_category_id' => 'required|numeric',
            'dish_price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'dish_image' => 'image|mimes:jpeg|max:200'
        ]);

        $dish->name = $request->dish_name;
        $dish->description = $request->dish_description;
        $dish->dish_category_id = $request->dish_category_id;
        $dish->price = $request->dish_price;

        if ($request->hasFile('dish_image')) {

            $this->deleteOldImage('images/dishes/', $dish->image); // if exists, delete old image

            // and upload new image
            $filename = $this->uploadImage($request, 'dish_image', $dish->name, 'images/dishes/');
            $dish->image = $filename;
            $dish->save();
        }

        return response()->json([
            'message' => __('Dish Updated'),
            'dish' => $dish
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dish $dish)
    {
        $this->authorize('delete', $dish);

        $this->deleteOldImage('images/dishes/', $dish->image);
        $dish->delete();
        return response()->json([
            'message' => __('Dish Deleted')
        ], 200);
    }
}
