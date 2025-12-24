<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	    Schema::defaultStringLength(191);
	    Relation::morphMap([
		    'User'  => 'App\Models\User',
		    'Contract' => 'App\Models\Contract'
	    ]);

	    VerifyEmail::toMailUsing(function ($notifiable, $url) {
		    return (new MailMessage)
			    ->subject('Verify Email Address')
			    ->line('Click the button below to verify your email address.')
			    ->action('Verify Email Address', $url);
	    });

    }
}
