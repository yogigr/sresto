<?php

namespace App\Policies;

use App\User;
use App\DishCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class DishCategoryPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the dishCategory.
     *
     * @param  \App\User  $user
     * @param  \App\DishCategory  $dishCategory
     * @return mixed
     */
    public function view(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create dishCategories.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isManager() ? true : false;
    }

    /**
     * Determine whether the user can update the dishCategory.
     *
     * @param  \App\User  $user
     * @param  \App\DishCategory  $dishCategory
     * @return mixed
     */
    public function update(User $user, DishCategory $dishCategory)
    {
        if ($user->isManager()) {
            return $user->id === $dishCategory->user_id;
        }
    }

    /**
     * Determine whether the user can delete the dishCategory.
     *
     * @param  \App\User  $user
     * @param  \App\DishCategory  $dishCategory
     * @return mixed
     */
    public function delete(User $user, DishCategory $dishCategory)
    {
        if ($user->isManager()) {
            return $user->id === $dishCategory->user_id;
        }
    }
}
