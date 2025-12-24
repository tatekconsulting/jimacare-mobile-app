<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationReceived extends Notification implements ShouldQueue
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
        $carer = $this->application->carer;
        $carerName = ($carer->firstname ?? '') . ' ' . ($carer->lastname ?? '');
        $jobTitle = $contract->title ?? 'Your Job Posting';
        
        // Count total applications for this job
        $totalApplications = \App\Models\JobApplication::where('contract_id', $contract->id)
            ->where('status', 'pending')
            ->count();

        return (new MailMessage)
            ->subject("New Application Received - {$jobTitle}")
            ->greeting("Hello {$notifiable->firstname},")
            ->line("You have received a new application for your job posting!")
            ->line("**Application Details:**")
            ->line("• **Applicant:** {$carerName}")
            ->line("• **Job:** {$jobTitle}")
            ->line("• **Total Applications:** {$totalApplications}")
            ->line("• **Proposed Rate:** £" . number_format($this->application->proposed_rate ?? 0, 2) . "/hour")
            ->when($this->application->cover_letter, function ($mail) {
                return $mail->line("• **Cover Letter:** " . \Str::limit($this->application->cover_letter, 150));
            })
            ->action('Review Applications', url(route('job-applications.index')))
            ->line("You can review all applications and select the best candidate for your job.")
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
            'carer_id' => $this->application->carer_id,
        ];
    }
}

