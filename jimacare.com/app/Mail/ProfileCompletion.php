<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfileCompletion extends Mailable
{
	use Queueable, SerializesModels;

	public $user;



	public function __construct(User $user)
	{
		$this->user = $user;

	}

	public function build()
	{
		return $this->markdown('emails.profile_completion')
			->subject($this->user->firstname . " just completed profile");
	}
}
