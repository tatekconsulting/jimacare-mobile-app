<?php

namespace App\Broadcasting;

use App\Models\Inbox;
use App\Models\User;

class InboxChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $client
     * @return array|bool
     */
    public function join(User $user, User $client)
    {
    	return $user->id == $client->id;
    }
}
