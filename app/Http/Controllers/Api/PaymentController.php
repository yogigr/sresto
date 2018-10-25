<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;
use App\Http\Controllers\Controller;
use App\Payment;

class PaymentController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        return response()->json([
            'payment' => $payment
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $this->authorize('update', $payment);

        $payment->amount = $request->payment_amount;
        $payment->change = $request->payment_change;
        $payment->payment_method_id = $request->payment_method_id;   
        $payment->save();

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

        return response()->json([
            'message' => __('Payment Updated'),
            'payment' => $payment,
            'order' => $payment->order
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);

        $payment->delete();
        return response()->json([
            'message' => __('Payment Deleted')
        ], 200);
    }
}
