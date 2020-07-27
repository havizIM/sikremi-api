<?php

namespace App\Policies;

use App\User;
use App\WorkOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkOrderPolicy
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
        return $user->roles == 'ADMINISTRATOR' || $user->roles == 'PARTNER';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\WorkOrder  $workOrder
     * @return mixed
     */
    public function view(User $user, WorkOrder $workOrder)
    {
        return $user->roles == 'ADMINISTRATOR' || $user->roles == 'PARTNER' || $user->roles == 'ENGINEER';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'PARTNER';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\WorkOrder  $workOrder
     * @return mixed
     */
    public function update(User $user, WorkOrder $workOrder)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\WorkOrder  $workOrder
     * @return mixed
     */
    public function delete(User $user, WorkOrder $workOrder)
    {
        return $user->roles == 'PARTNER';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\WorkOrder  $workOrder
     * @return mixed
     */
    public function restore(User $user, WorkOrder $workOrder)
    {
        return $user->roles == 'ADMINISTRATOR';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\WorkOrder  $workOrder
     * @return mixed
     */
    public function forceDelete(User $user, WorkOrder $workOrder)
    {
        return $user->roles == 'ADMINISTRATOR';
    }
}
