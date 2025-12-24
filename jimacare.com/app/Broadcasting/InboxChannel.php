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
     * @param  int|string  $clientId  The user ID from the channel route parameter
     * @return array|bool
     */
    public function join(User $user, $clientId)
    {
        // Convert clientId to integer for comparison
        $clientId = (int) $clientId;
        
        // User can access their own inbox channel
        if ($user->id == $clientId) {
            return true;
        }
        
        // Also check if there's an inbox between the authenticated user and the channel owner
        // This allows both parties in a conversation to receive messages
        $inbox = Inbox::where(function ($q) use ($user, $clientId) {
            return $q->where('client_id', $user->id)->where('seller_id', $clientId);
        })->orWhere(function ($q) use ($user, $clientId) {
            return $q->where('seller_id', $user->id)->where('client_id', $clientId);
        })->first();
        
        return $inbox !== null;
    }
}
