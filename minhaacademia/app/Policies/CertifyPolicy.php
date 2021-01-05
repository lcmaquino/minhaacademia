<?php

namespace App\Policies;

use App\Certify;
use App\Course;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertifyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user = null)
    {
        return !empty($user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Certify  $certify
     * @return mixed
     */
    public function view(User $user = null, Certify $certify = null)
    {
        return !empty($user) && ($user->isAdmin() || $certify->cpf == $user->cpf);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Certify $certify
     * @return mixed
     */
    public function delete(User $user, Certify $certify)
    {
        return $this->forceDelete($user, $certify);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Certify $certify
     * @return mixed
     */
    public function forceDelete(User $user, Certify $certify)
    {
        return ($user->isAdmin());
    }
}
