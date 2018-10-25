<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;
use App\GiftCard;

class GiftCardPeriodeCheck implements Rule
{
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
        $expirationDate = Carbon::createFromFormat('Y-m-d', $giftCard->expiration_date);
        if ($expirationDate->greaterThanOrEqualTo(Carbon::today())) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'card period has expired';
    }
}
