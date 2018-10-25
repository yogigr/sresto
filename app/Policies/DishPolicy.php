<?php

namespace App\Policies;

use App\User;
use App\Dish;
use Illuminate\Auth\Access\HandlesAuthorization;

class DishPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the dish.
     *
     * @param  \App\User  $user
     * @param  \App\Dish  $dish
     * @return mixed
     */
    public function view(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create dishes.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isManager() ? true : false;
    }

    /**
     * Determine whether the user can update the dish.
     *
     * @param  \App\User  $user
     * @param  \App\Dish  $dish
     * @return mixed
     */
    public function update(User $user, Dish $dish)
    {
        if ($user->isManager()) {
            return $user->id === $dish->user_id;
        }
    }

    /**
     * Determine whether the user can delete the dish.
     *
     * @param  \App\User  $user
     * @param  \App\Dish  $dish
     * @return mixed
     */
    public function delete(User $user, Dish $dish)
    {
        if ($user->isManager()) {
            return $user->id === $dish->user_id;
        }
    }
}
