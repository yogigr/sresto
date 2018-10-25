<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Cart;
use Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Cart::class);

        $carts = Auth::user()->carts()->get();
        return response()->json([
            'carts' => $carts
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
        $this->authorize('create', Cart::class);

        if (Cart::where('user_id', Auth::id())->where('dish_id', $request->dish_id)->exists()) {
            $cart = Cart::where('user_id', Auth::id())->where('dish_id', $request->dish_id)->first();
            $cart->quantity += 1;
            $cart->save();
        } else {
            $cart = Cart::create([
                'dish_id' => $request->dish_id,
                'quantity' => 1,
                'user_id' => Auth::id()
            ]);
        }

        return response()->json([
            'cart' => $cart
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        $this->authorize('update', $cart);

        $request->validate([
            'quantity' => 'required|numeric' 
        ]);

        $cart->quantity = $request->quantity;
        $cart->save();
        return response()->json([
            'cart' => $cart,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        $this->authorize('delete', $cart);
        
        $cart->delete();
        return response()->json([
            'message' => __('Cart Deleted')
        ], 200);
    }
}
