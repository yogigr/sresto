<?php

namespace App\Policies;

use App\User;
use App\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the payment.
     *
     * @param  \App\User  $user
     * @param  \App\Payment  $payment
     * @return mixed
     */
    public function view(User $user, Payment $payment)
    {
        return true;
    }

    /**
     * Determine whether the user can create payments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the payment.
     *
     * @param  \App\User  $user
     * @param  \App\Payment  $payment
     * @return mixed
     */
    public function update(User $user, Payment $payment)
    {
        if ($user->isManager()) {
            return true;
        }

        if ($user->isChef()) {
            return false;
        }

        if ($user->isWaiter()) {
            return $user->id === $payment->order->waiter_id;
        }

        return $user->id === $payment->user_id
    }

    /**
     * Determine whether the user can delete the payment.
     *
     * @param  \App\User  $user
     * @param  \App\Payment  $payment
     * @return mixed
     */
    public function delete(User $user, Payment $payment)
    {
        if ($user->isManager()) {
            return true;
        }

        if ($user->isChef()) {
            return false;
        }

        if ($user->isWaiter()) {
            return $user->id === $payment->order->waiter_id;
        }

        return $user->id === $payment->user_id
    }
}
