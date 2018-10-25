<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrderDetail;
use App\Order;

class OrderDetailController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        if (OrderDetail::where('order_id', $order->id)->where('dish_id', $request->dish_id)->exists()) {
            $orderDetail = OrderDetail::where('order_id', $order->id)->where('dish_id', $request->dish_id)->first();
            $orderDetail->quantity += 1;
            $orderDetail->save();
        } else {
            $orderDetail = OrderDetail::create([
                'dish_id' => $request->dish_id,
                'quantity' => 1,
                'price' => $request->price,
                'order_id' => $order->id
            ]);
        }

        return response()->json([
            'orderDetail' => $orderDetail
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderDetail $orderDetail)
    {
        $this->authorize('update', $orderDetail->order);

        $request->validate([
            'quantity' => 'required|numeric',
            'price' => 'required|regex:/^\d*(\.\d{1,2})?$/'
        ]);

        $orderDetail->quantity = $request->quantity;
        $orderDetail->price = $request->price;
        $orderDetail->save();

        return response()->json([
            'orderDetail' => $orderDetail
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderDetail $orderDetail)
    {
        $this->authorize('update', $orderDetail->order);

        $orderDetail->delete();
        return response()->json([
            'message' => __('Order Detail Deleted')
        ], 200);
    }
}
