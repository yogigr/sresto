<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $fillable = [
    	'card_number', 'value', 'expiration_date', 'user_id'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function payments()
    {
    	return $this->hasMany('App\Payment');
    }
}
