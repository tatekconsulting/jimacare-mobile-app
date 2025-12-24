<?php

namespace App\Mail;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentInfo extends Mailable
{
    use Queueable, SerializesModels;

	public $document;
	public function __construct($document)
	{
		$this->document = $document;

	}

	public function build()
	{
			return $this->view('emails.document_info')
			->subject($this->document->user->firstname .' '.$this->document->user->lastname. " has uploaded a new document")->attachFromStorageDisk('s3',$this->document->path);
	}
}
