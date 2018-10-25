<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
    	'name', 'email', 'phone', 'address', 'user_id'
    ];

    protected $with = ['user'];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function orders()
    {
    	return $this->hasMany('App\Order');
    }
}
