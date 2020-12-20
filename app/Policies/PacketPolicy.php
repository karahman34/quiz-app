<?php

namespace App\Policies;

use App\Models\Packet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PacketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Packet  $packet
     * @return mixed
     */
    public function view(User $user, Packet $packet)
    {
        return (int) $user->id === (int) $packet->user_id;
    }

    /**
     * Determine whether the user can create the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Packet  $packet
     * @return mixed
     */
    public function create(User $user, Packet $packet)
    {
        return (int) $user->id === (int) $packet->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Packet  $packet
     * @return mixed
     */
    public function update(User $user, Packet $packet)
    {
        return (int) $user->id === (int) $packet->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Packet  $packet
     * @return mixed
     */
    public function delete(User $user, Packet $packet)
    {
        return (int) $user->id === (int) $packet->user_id;
    }
}
