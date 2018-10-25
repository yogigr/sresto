<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
    	'order_id', 'dish_id', 'quantity', 'price'
    ];

    //relation
    public function order()
    {
    	return $this->belongsTo('App\Order');
    }

    public function  dish()
    {
    	return $this->belongsTo('App\Dish');
    }
}
