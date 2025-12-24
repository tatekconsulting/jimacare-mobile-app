<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationNotSelected extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $contract = $this->application->contract;
        $jobTitle = $contract->title ?? 'Job Opportunity';

        return (new MailMessage)
            ->subject("Application Update - {$jobTitle}")
            ->greeting("Hello {$notifiable->firstname},")
            ->line("Thank you for your interest in the job: **{$jobTitle}**")
            ->line("The client has selected another candidate for this position.")
            ->line("Don't worry - there are many more opportunities available!")
            ->action('Browse More Jobs', url(route('contract.index')))
            ->line("Keep applying and you'll find the perfect match!")
            ->line('Thank you for using JimaCare!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'application_id' => $this->application->id,
            'contract_id' => $this->application->contract_id,
        ];
    }
}

