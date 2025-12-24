<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DbsTraining extends Mailable
{
	use Queueable, SerializesModels;
	public $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		return $this->markdown('emails.dbs_training')
		->subject("DBS Training Certificate");
    }
}
