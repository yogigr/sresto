<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\GiftCard;
use App\Order;

class GiftCardBalanceCheck implements Rule
{
    public $order;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $giftCard = GiftCard::find($value);
        $paymentTotal = $giftCard->payments->sum('amount') - $giftCard->payments->sum('change');
        $balance = $giftCard->value - $paymentTotal;
        if ($balance < ($this->order->subtotal + $this->order->tax - $this->order->discount)) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'insufficient gift card balance.';
    }
}
