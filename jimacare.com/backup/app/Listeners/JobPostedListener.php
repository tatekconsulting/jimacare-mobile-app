<?php

namespace App\Listeners;

use App\Events\JobPostedEvent;
use App\Models\User;
use App\Notifications\JobPostedNotice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class JobPostedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  JobPostedEvent  $event
     * @return void
     */
    public function handle(JobPostedEvent $event)
    {
		if ($event->user_type !== 'admin') {
			$users = $event->contract->role->users;
		} else {
			$users = User::where(['role_id' => 1, 'status' => 'active'])->get();
		}
		foreach ($users as $user) {
			$user->notify(new JobPostedNotice($event->contract));
		}
	}
}
