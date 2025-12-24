<?php

namespace App\Mail;

use App\Models\Reference;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefereeUpdate extends Mailable
{
    use Queueable, SerializesModels;
	public $user;
	public $reference;



	public function __construct(User $user,Reference $reference)
	{
		$this->user = $user;
		$this->reference = $reference;
	}

	public function build()
	{
		return $this->markdown('emails.referee_update')
			->subject($this->reference->first_name . " has confirmed himself as reference");
	}
}
