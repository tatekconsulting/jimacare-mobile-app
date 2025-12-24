<?php

namespace App\Providers;

use App\Events\MessageEvent;
use App\Listeners\MessageListener;
use App\Events\JobPostedEvent;
use App\Listeners\JobPostedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'Illuminate\Auth\Events\Verified' => [
	        'App\Listeners\LogVerifiedUser',
        ],

        MessageEvent::class => [
        	MessageListener::class
        ],
        JobPostedEvent::class => [
	        JobPostedListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
