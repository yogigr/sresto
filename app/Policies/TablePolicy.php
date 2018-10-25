<?php

namespace App\Policies;

use App\User;
use App\Table;
use Illuminate\Auth\Access\HandlesAuthorization;

class TablePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the table.
     *
     * @param  \App\User  $user
     * @param  \App\Table  $table
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->isChef() ? false : true;
    }

    /**
     * Determine whether the user can create tables.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isManager() ? true : false;
    }

    /**
     * Determine whether the user can update the table.
     *
     * @param  \App\User  $user
     * @param  \App\Table  $table
     * @return mixed
     */
    public function update(User $user, Table $table)
    {
        if ($user->isManager()) {
            return $user->id === $table->user_id;
        }
    }

    /**
     * Determine whether the user can delete the table.
     *
     * @param  \App\User  $user
     * @param  \App\Table  $table
     * @return mixed
     */
    public function delete(User $user, Table $table)
    {
        if ($user->isManager()) {
            return $user->id === $table->user_id;
        }
    }
}
