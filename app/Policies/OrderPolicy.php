<?php

namespace App\Policies;

use App\User;
use App\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function view(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create orders.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isChef() ? false : true;
    }

    /**
     * Determine whether the user can update the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function update(User $user, Order $order)
    {
        if ($user->isManager()) {
            return true;
        }

        if ($user->isChef()) {
            return false;
        }

        if ($user->isWaiter()) {
            return $user->id === $order->waiter_id;
        }
    }

    /**
     * Determine whether the user can delete the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function delete(User $user)
    {
        if ($user->isManager()) {
            return true;
        }
    }

     /**
     * Determine whether the user can accept the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function accept(User $user)
    {
        return $user->isWaiter() ? false : true;
    }

    /**
     * Determine whether the user can reject the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function reject(User $user)
    {
        return $user->isWaiter() ? false : true;
    }

    /**
     * Determine whether the user can cook the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function cook(User $user, Order $order)
    {
        if ($user->isWaiter()) {
            return false;
        }

        return $user->id === $order->chef_id;
    }

    /**
     * Determine whether the user can set cooked the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function setCooked(User $user, Order $order)
    {
        if ($user->isWaiter()) {
            return false;
        }

        return $user->id === $order->chef_id;
    }

    /**
     * Determine whether the user can set finished the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function setFinished(User $user, Order $order)
    {
        if ($user->isChef()) {
            return false;
        }

        return ($user->id == $order->waiter_id) && ($order->order_status_id == 5);
    }

    /**
     * Determine whether the user can pay the order.
     *
     * @param  \App\User  $user
     * @param  \App\Order  $order
     * @return mixed
     */
    public function pay(User $user, Order $order)
    {
        if ($user->isChef()) {
            return false;
        }

        return $user->id == $order->waiter_id;
    }
}
