<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = [
    	'code', 'name', 'description', 'dish_category_id', 'price', 'user_id', 'image', 'is_in_stock'
    ];

    //relationship
    public function dishCategory()
    {
    	return $this->belongsTo('App\DishCategory');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function orderDetails()
    {
        return $this->hasMany('App\OrderDetail');
    }

    public function carts()
    {
        return $this->hasMany('App\Cart');
    }
}
