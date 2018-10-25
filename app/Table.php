<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
    	'name', 'user_id', 'is_in_use'
    ];

    protected $with = ['user'];

    //relationship
    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function orders()
    {
    	return $this->hasMany('App\Order');
    }
}
