<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSelected extends Notification implements ShouldQueue
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
        $client = $contract->user;
        $clientName = ($client->firstname ?? '') . ' ' . ($client->lastname ?? '');
        $jobTitle = $contract->title ?? 'Job Opportunity';

        return (new MailMessage)
            ->subject("🎉 Congratulations! You've Been Selected - {$jobTitle}")
            ->greeting("Hello {$notifiable->firstname},")
            ->line("Great news! Your application has been selected!")
            ->line("**Job Details:**")
            ->line("• **Job:** {$jobTitle}")
            ->line("• **Client:** {$clientName}")
            ->line("• **Location:** " . ($contract->user->postcode ?? $contract->user->city ?? 'N/A"))
            ->line("• **Rate:** £" . number_format($this->application->proposed_rate ?? $contract->hourly_rate ?? 0, 2) . "/hour")
            ->action('Contact Client', url(route('inbox.show', ['user' => $client->id])))
            ->line("The client has selected you for this position. You can now contact them to discuss the details and get started!")
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

