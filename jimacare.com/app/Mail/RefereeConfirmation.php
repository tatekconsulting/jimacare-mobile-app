<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefereeConfirmation extends Mailable
{
    use Queueable, SerializesModels;

	public $user;
	public $data;
	public function __construct(User $user , $data)
	{
		$this->user = $user;
		$this->data = $data;
	}

	public function build()
	{
		return $this->markdown('emails.referee_confirmation')
			->subject("Reference Approval Request");
	}
}
