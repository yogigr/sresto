<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    	'order_id', 'amount', 'change', 'payment_method_id', 
    	'credit_card_number', 'credit_card_expiration_year', 'credit_card_expiration_month', 'credit_card_cvc',
    	'gift_card_id', 'user_id'
    ];

    protected $with =  ['giftCard'];

    public function order()
    {
    	return $this->belongsTo('App\Order');
    }

    public function method()
    {
    	return $this->belongsTo('App\PaymentMethod');
    }

    public function giftCard()
    {
    	return $this->belongsTo('App\GiftCard');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
