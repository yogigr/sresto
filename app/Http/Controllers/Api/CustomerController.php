<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Customer;
use Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Customer::class);

        $customers = Customer::all();
        return response()->json([
            'customers' => $customers
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
        $this->authorize('create', Customer::class);

        $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|string|email|unique:customers,email',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
        ]);

        $customer = Customer::create([
            'name' => $request->customer_name,
            'email' => $request->customer_email,
            'phone' => $request->customer_phone,
            'address' => $request->customer_address,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'message' => __('Customer Created'),
            'customer' => $customer
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

        return response()->json([
            'customer' => $customer
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|string|email|unique:customers,email,' . $customer->id,
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
        ]);
       
        $customer->name = $request->customer_name;
        $customer->email = $request->customer_email;
        $customer->phone = $request->customer_phone;
        $customer->address = $request->customer_address;
        $customer->save();
    
        return response()->json([
            'message' => __('Customer Updated'),
            'customer' => $customer
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        
        $customer->delete();
        return response()->json([
            'message' => __('Customer Deleted')
        ], 200);
    }
}
