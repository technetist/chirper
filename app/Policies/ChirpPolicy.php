<?php

namespace App\Policies;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ChirpPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Chirp $chirp
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Chirp $chirp): Response|bool
    {
        return $chirp->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Chirp $chirp
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Chirp $chirp): Response|bool
    {
        return $this->update($user, $chirp);
    }
}
