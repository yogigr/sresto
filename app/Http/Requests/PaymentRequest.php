<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardNumber;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardExpirationMonth;
use App\Rules\GiftCardPeriodeCheck;
use App\Rules\GiftCardBalanceCheck;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_amount' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'payment_change' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'payment_method_id' => 'required|numeric',
            'credit_card_number' => ['required_if:payment_method_id,2', new CardNumber],
            'credit_card_expiration_year' => ['required_if:payment_method_id,2', new CardExpirationYear($this->get('credit_card_expiration_month'))],
            'credit_card_expiration_month' => ['required_if:payment_method_id,2', new CardExpirationMonth($this->get('credit_card_expiration_year'))],
            'credit_card_cvc' => ['required_if:payment_method_id,2', new CardCvc($this->get('credit_card_number'))],
            'gift_card_id' => [
                'required_if:payment_method_id,3', 'numeric', new GiftCardPeriodeCheck, new GiftCardBalanceCheck($this->order)
            ]
        ];
    }
}
