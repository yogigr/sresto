<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DishCategory extends Model
{
    protected $fillable = ['name', 'description', 'user_id'];

    //relationship
    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function dishes()
    {
    	return $this->hasMany('App\Dish');
    }
}
