<?php

namespace App\Policies;

use App\PartnerUser;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartnerUserPolicy
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
     * @param  \App\PartnerUser  $partnerUser
     * @return mixed
     */
    public function view(User $user, PartnerUser $partnerUser)
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
     * @param  \App\PartnerUser  $partnerUser
     * @return mixed
     */
    public function update(User $user, PartnerUser $partnerUser)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\PartnerUser  $partnerUser
     * @return mixed
     */
    public function delete(User $user, PartnerUser $partnerUser)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\PartnerUser  $partnerUser
     * @return mixed
     */
    public function restore(User $user, PartnerUser $partnerUser)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\PartnerUser  $partnerUser
     * @return mixed
     */
    public function forceDelete(User $user, PartnerUser $partnerUser)
    {
        return $user->roles == 'ADMINISTRATOR';
    }
}
