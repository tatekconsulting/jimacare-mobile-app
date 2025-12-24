<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobPostedNotice extends Notification //implements ShouldQueue
{
    use Queueable;
    public $contract;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
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
        $clientName = $this->contract->user->firstname ?? 'A client';
        $jobType = $this->contract->role->title ?? 'care professional';
        $location = $this->contract->user->postcode ?? $this->contract->user->city ?? 'your area';
        $jobTitle = $this->contract->title ?? 'New Job Opportunity';
        
        // Determine greeting and message based on recipient role
        $greeting = 'Hello';
        $introMessage = "A new job opportunity has been posted on JimaCare!";
        
        if ($notifiable->role_id == 1) {
            // Admin receives all job notifications
            $greeting = 'Admin';
            $introMessage = "A new {$jobType} job has been posted on JimaCare.";
        } elseif ($notifiable->role_id == 3) {
            // Carer
            $greeting = $notifiable->firstname ?? 'Carer';
            $introMessage = "A new Carer job opportunity has been posted that matches your profile!";
        } elseif ($notifiable->role_id == 4) {
            // Childminder
            $greeting = $notifiable->firstname ?? 'Childminder';
            $introMessage = "A new Childminder job opportunity has been posted that matches your profile!";
        } elseif ($notifiable->role_id == 5) {
            // Housekeeper
            $greeting = $notifiable->firstname ?? 'Housekeeper';
            $introMessage = "A new Housekeeper job opportunity has been posted that matches your profile!";
        }

        return (new MailMessage)
            ->subject("New {$jobType} Job Posted - {$jobTitle}")
            ->greeting("{$greeting},")
            ->line($introMessage)
            ->line("**Job Details:**")
            ->line("• **Job Type:** {$jobType}")
            ->line("• **Title:** {$jobTitle}")
            ->line("• **Client:** {$clientName}")
            ->line("• **Location:** {$location}")
            ->when($this->contract->hourly_rate || $this->contract->daily_rate || $this->contract->weekly_rate, function ($mail) use ($notifiable) {
                // Show provider rate to service providers, full breakdown to admins
                $isServiceProvider = in_array($notifiable->role_id, [3, 4, 5]);
                $isAdmin = $notifiable->role_id == 1;
                $rateInfo = $this->contract->getDisplayRate($isServiceProvider);
                
                if ($isServiceProvider) {
                    $clientRate = $this->contract->hourly_rate ?? $this->contract->daily_rate ?? $this->contract->weekly_rate ?? 0;
                    $minimumRate = $this->contract->getMinimumProviderRate();
                    $rawProviderRate = ($clientRate * 66.6667) / 100;
                    $isMinimumEnforced = $rateInfo['type'] === 'hourly' && $rawProviderRate < $minimumRate;
                    
                    $rateMessage = "• **Your Rate:** " . $rateInfo['formatted'] . " (66.6% of client's price)";
                    if ($isMinimumEnforced) {
                        $rateMessage .= "\n• **✓ Minimum Rate:** £" . number_format($minimumRate, 2) . "/hour guaranteed";
                    }
                    $rateMessage .= "\n• **Client Posted:** £" . number_format($clientRate, 2) . "/" . $rateInfo['type'];
                    
                    return $mail->line($rateMessage);
                } elseif ($isAdmin) {
                    $pricingBreakdown = $this->contract->getPricingBreakdown();
                    $rateMessage = "• **Client Posted:** £" . number_format($pricingBreakdown['client_rate'], 2) . "/" . $pricingBreakdown['type'];
                    $rateMessage .= "\n• **Provider Receives (66.6%):** £" . number_format($pricingBreakdown['provider_rate'], 2) . "/" . $pricingBreakdown['type'];
                    $rateMessage .= "\n• **Platform Fee (33.3333%):** £" . number_format($pricingBreakdown['platform_fee'], 2) . "/" . $pricingBreakdown['type'];
                    
                    return $mail->line($rateMessage);
                } else {
                    return $mail->line("• **Rate:** " . $rateInfo['formatted']);
                }
            })
            ->line("• **Description:** " . ($this->contract->desc ?? 'No description provided'))
            ->action('View Job Details & Apply', url(route('contract.show', ['contract' => $this->contract->id])))
            ->line('This is an automated notification from JimaCare. Thank you for being part of our platform!');
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
            //
        ];
    }
}
