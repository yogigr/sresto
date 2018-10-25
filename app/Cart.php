<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
    	'dish_id', 'quantity', 'user_id'
    ];

    //relation
    public function dish()
    {
    	return $this->belongsTo('App\Dish');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}
