<?php

namespace App\Policies;

use App\Models\Buisness;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuisnessPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Buisness  $buisness
     * @return mixed
     */
    public function view(User $user, Buisness $buisness)
    {
        return $buisness->user_id == $user->id || $buisness->users->where('id', $user->id)->count() > 0;
    }
}
