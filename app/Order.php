<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    	'code', 'customer_id', 'table_id', 'subtotal', 'tax', 'discount', 
    	'is_paid', 'waiter_id', 'chef_id', 'start_time', 'end_time', 'order_status_id'
    ];

    //relation
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function table()
    {
        return $this->belongsTo('App\Table');
    }

    public function payment()
    {
    	return $this->hasOne('App\Payment');
    }

    public function waiter()
    {
    	return $this->belongsTo('App\User', 'waiter_id');
    }

    public function chef()
    {
    	return $this->belongsTo('App\User', 'chef_id');
    }

    public function orderStatus()
    {
        return $this->belongsTo('App\OrderStatus');
    }

    public function orderDetails()
    {
        return $this->hasMany('App\OrderDetail');
    }
}
