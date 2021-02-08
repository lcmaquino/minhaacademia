<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class LessonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return mixed
     */
    public function view(User $user = null, Lesson $lesson)
    {   
        return ($user != null && ($lesson->module()->course()->visibility == 1 || !$user->isStudent()));
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
     * @param  \App\Models\Lesson  $lesson
     * @return mixed
     */
    public function update(User $user, Lesson $lesson)
    {
        return ($user->isAdmin() || ($lesson->module()->course()->teacher === $user->id));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return mixed
     */
    public function delete(User $user, Lesson $lesson)
    {
        return $this->update($user, $lesson);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return mixed
     */
    public function restore(User $user, Lesson $lesson)
    {
        return $this->update($user, $lesson);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Lesson  $lesson
     * @return mixed
     */
    public function forceDelete(User $user, Lesson $lesson)
    {
        return $this->update($user, $lesson);
    }
}
