<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificationExpiringAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public $document;
    public $user;
    public $daysUntilExpiry;

    /**
     * Create a new notification instance.
     *
     * @param Document $document
     * @param int $daysUntilExpiry
     */
    public function __construct(Document $document, int $daysUntilExpiry)
    {
        $this->document = $document;
        $this->user = $document->user;
        $this->daysUntilExpiry = $daysUntilExpiry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $userName = trim(($this->user->firstname ?? '') . ' ' . ($this->user->lastname ?? ''));
        $userRole = $this->user->role->title ?? 'Service Provider';
        $documentName = $this->document->name ?? 'Certification';
        $expirationDate = $this->document->expiration ? $this->document->expiration->format('d/m/Y') : 'N/A';
        
        $daysText = $this->daysUntilExpiry == 1 ? 'day' : 'days';
        
        return (new MailMessage)
            ->subject("⚠️ Certification Expiring: {$documentName} - {$userName}")
            ->line("A certification for a {$userRole} is expiring soon.")
            ->line("**Service Provider:** {$userName}")
            ->line("**Email:** {$this->user->email}")
            ->line("**Certification:** {$documentName}")
            ->line("**Expiration Date:** {$expirationDate}")
            ->line("**Days Until Expiry:** {$this->daysUntilExpiry} {$daysText}")
            ->action('View User Profile', url('/dashboard/user/' . $this->user->id))
            ->line('Please contact the service provider to renew their certification before it expires.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $userName = trim(($this->user->firstname ?? '') . ' ' . ($this->user->lastname ?? ''));
        $userRole = $this->user->role->title ?? 'Service Provider';
        $documentName = $this->document->name ?? 'Certification';
        $expirationDate = $this->document->expiration ? $this->document->expiration->format('d/m/Y') : 'N/A';
        
        $daysText = $this->daysUntilExpiry == 1 ? 'day' : 'days';
        
        return [
            'type' => 'certification_expiring',
            'title' => "Certification Expiring: {$documentName}",
            'message' => "{$userName} ({$userRole}) has a certification '{$documentName}' expiring in {$this->daysUntilExpiry} {$daysText} (Expires: {$expirationDate})",
            'action_url' => route('dashboard.user.show', ['user' => $this->user->id]),
            'data' => [
                'document_id' => $this->document->id,
                'user_id' => $this->user->id,
                'user_name' => $userName,
                'user_role' => $userRole,
                'document_name' => $documentName,
                'expiration_date' => $expirationDate,
                'days_until_expiry' => $this->daysUntilExpiry,
            ],
        ];
    }
}

