<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Traits\CodeGenerator;
use App\OrderDetail;
use App\Order;
use App\Payment;
use Auth;

class OrderController extends Controller
{
    use CodeGenerator;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Order::class);

        $orders = Order::all();
        return response()->json([
            'orders' => $orders
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
        $this->authorize('create', Order::class);

        $request->validate([
            'order_subtotal' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'order_tax' => 'regex:/^\d*(\.\d{1,2})?$/',
            'order_discount' => 'regex:/^\d*(\.\d{1,2})?$/'
        ]);

        $order = Order::create([
            'code' => $this->getCodeWithDatetime('OR', 'orders'),
            'customer_id' => $request->order_customer_id,
            'table_id' => $request->order_table_id,
            'subtotal' => $request->order_subtotal,
            'tax' => $request->order_tax,
            'discount' => $request->order_discount,
            'waiter_id' => Auth::id(),
            'start_time' => now()
        ]);

        //insert carts to order details;
        $carts = Auth::user()->carts()->get();
        foreach ($carts as $cart) {
            OrderDetail::create([
                'order_id' => $order->id,
                'dish_id' => $cart->dish->id,
                'quantity' => $cart->quantity,
                'price' => $cart->dish->price
            ]);

            //delete cart
            $cart->delete();
        }

        $order->table->is_in_use = true;
        $order->table->save();

        return response()->json([
            'message' => __('Order Created'),
            'order' => $order
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        return response()->json([
            'order' => $order
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'order_subtotal' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'order_tax' => 'regex:/^\d*(\.\d{1,2})?$/',
            'order_discount' => 'regex:/^\d*(\.\d{1,2})?$/'
        ]);

        $order->customer_id = $request->order_customer_id;
        $order->table->table_id = $request->order_table_id;
        $order->subtotal = $request->order_subtotal;
        $order->tax = $request->order_tax;
        $order->discount = $request->order_discount;
        $order->save();

        return response()->json([
            'message' => __('Order updated'),
            'order' => $order
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        $order->delete();
        return response()->json([
            'message' => __('Order Deleted')
        ]);
    }

    /**
     * set order status to be accepted.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function accept(Order $order)
    {
        $this->authorize('accept', $order);

        $order->order_status_id = 2;
        $order->chef_id = Auth::id();
        $order->save();

        return response()->json([
            'message' => __('Order Accepted'),
            'order' => $order
        ]);
    }

    /**
     * set order status to be rejected.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function reject(Order $order)
    {
        $this->authorize('reject', $order);

        $order->order_status_id = 3;
        $order->save();

        return response()->json([
            'message' => __('Order Rejected'),
            'order' => $order
        ]);
    }

    /**
     * set order status to cook.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function cook(Order $order)
    {
        $this->authorize('cook', $order);

        $order->order_status_id = 4;
        $order->save();

        return response()->json([
            'message' => __('orders are in the process of cooking'),
            'order' => $order
        ]);
    }

    /**
     * set order status to be cooked.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function setCooked(Order $order)
    {
        $this->authorize('setCooked', $order);

        $order->order_status_id = 5;
        $order->save();

        return response()->json([
            'message' => __('Order status already cooked'),
            'order' => $order
        ]);
    }

    /**
     * set order status to be Finished.
     *
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function setFinished(Order $order)
    {
        $this->authorize('setFinished', $order);

        $order->order_status_id = 6;
        $order->end_time = now();
        $order->save();

        return response()->json([
            'message' => __('The Order is Completed'),
            'order' => $order
        ]);
    }

    /**
     * Pay the order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function pay(PaymentRequest $request, Order $order)
    {
        $this->authorize('pay', $order);
        
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $request->payment_amount,
            'change' => $request->payment_change,
            'payment_method_id' => $request->payment_method_id,
            'user_id' => Auth::id()
        ]);

        if ($payment->payment_method_id == 2) {
            $payment->credit_card_number = $request->credit_card_number;
            $payment->credit_card_expiration_year = $request->credit_card_expiration_year;
            $payment->credit_card_expiration_month = $request->credit_card_expiration_month;
            $payment->credit_card_cvc = $request->credit_card_cvc;
            $payment->save();
        }

        if ($payment->payment_method_id == 3) {
            $payment->gift_card_id = $request->gift_card_id;
            $payment->save();
        }

        $order->is_paid = true;
        $order->save();

        return response()->json([
            'message' => __('order paid successfully'),
            'payment' => $payment,
            'order' => $order
        ], 200);
    }

}
