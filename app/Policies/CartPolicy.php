<?php

namespace App\Policies;

use App\User;
use App\Cart;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the cart.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->isChef() ? false : true;
    }

    /**
     * Determine whether the user can create carts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isChef() ? false : true;
    }

    /**
     * Determine whether the user can update the cart.
     *
     * @param  \App\User  $user
     * @param  \App\Cart  $cart
     * @return mixed
     */
    public function update(User $user, Cart $cart)
    {
        if ($user->isChef()) {
            return false;
        }

        return $user->id === $cart->user_id;
    }

    /**
     * Determine whether the user can delete the cart.
     *
     * @param  \App\User  $user
     * @param  \App\Cart  $cart
     * @return mixed
     */
    public function delete(User $user, Cart $cart)
    {
        if ($user->isChef()) {
            return false;
        }

        return $user->id === $cart->user_id;
    }
}
