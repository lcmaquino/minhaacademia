<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return mixed
     */
    public function view(User $user = null, Activity  $activity)
    {
        return ($user != null && ($activity->module()->course()->visibility == 1 || !$user->isStudent()));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return mixed
     */
    public function update(User $user, Activity $activity)
    {
        return ($user->isAdmin() || ($activity->module()->course()->teacher === $user->id));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return mixed
     */
    public function delete(User $user, Activity $activity)
    {
        return $this->update($user, $activity);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return mixed
     */
    public function restore(User $user, Activity $activity)
    {
        return $this->update($user, $activity);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return mixed
     */
    public function forceDelete(User $user, Activity $activity)
    {
        return $this->update($user, $activity);
    }
}
