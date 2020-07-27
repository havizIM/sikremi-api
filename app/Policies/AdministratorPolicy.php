<?php

namespace App\Policies;

use App\Administrator;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdministratorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Administrator  $administrator
     * @return mixed
     */
    public function view(User $user, Administrator $administrator)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Administrator  $administrator
     * @return mixed
     */
    public function update(User $user, Administrator $administrator)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Administrator  $administrator
     * @return mixed
     */
    public function delete(User $user, Administrator $administrator)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Administrator  $administrator
     * @return mixed
     */
    public function restore(User $user, Administrator $administrator)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Administrator  $administrator
     * @return mixed
     */
    public function forceDelete(User $user, Administrator $administrator)
    {
        return $user->roles == 'ADMINISTRATOR';
    }
}
