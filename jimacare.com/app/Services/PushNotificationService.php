<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationService
{
    protected $webPush;

    public function __construct()
    {
        $vapidKeys = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ]
        ];

        if ($vapidKeys['VAPID']['publicKey'] && $vapidKeys['VAPID']['privateKey']) {
            $this->webPush = new WebPush($vapidKeys);
        }
    }

    /**
     * Send push notification to a specific user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        if (!$this->webPush || empty($user->push_subscription)) {
            return false;
        }

        try {
            $subscription = Subscription::create(json_decode($user->push_subscription, true));

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => asset('img/icons/icon-192x192.png'),
                'badge' => asset('img/icons/icon-72x72.png'),
                'url' => $data['url'] ?? '/',
                'tag' => $data['tag'] ?? 'jimacare-notification',
                'data' => $data
            ]);

            $this->webPush->sendOneNotification($subscription, $payload);

            return true;
        } catch (\Exception $e) {
            Log::error('Push notification failed: ' . $e->getMessage(), [
                'user_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * Send push notification to multiple users
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = []): int
    {
        if (!$this->webPush) {
            return 0;
        }

        $users = User::whereIn('id', $userIds)
            ->whereNotNull('push_subscription')
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            if ($this->sendToUser($user, $title, $body, $data)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * Send notification for new message
     */
    public function notifyNewMessage(User $recipient, User $sender): bool
    {
        return $this->sendToUser(
            $recipient,
            'New Message',
            "You have a new message from {$sender->name}",
            [
                'url' => route('inbox.show', ['user' => $sender->id]),
                'tag' => 'new-message-' . $sender->id
            ]
        );
    }

    /**
     * Send notification for new job posting
     */
    public function notifyNewJob(User $carer, $contract): bool
    {
        return $this->sendToUser(
            $carer,
            'New Job Posted',
            "A new {$contract->role->title} job is available in your area",
            [
                'url' => route('contract.show', ['contract' => $contract->id]),
                'tag' => 'new-job-' . $contract->id
            ]
        );
    }

    /**
     * Send notification for booking confirmation
     */
    public function notifyBookingConfirmed(User $user, $booking): bool
    {
        return $this->sendToUser(
            $user,
            'Booking Confirmed! ✓',
            "Your booking has been confirmed",
            [
                'url' => route('order.show', ['order' => $booking->id]),
                'tag' => 'booking-confirmed-' . $booking->id
            ]
        );
    }

    /**
     * Send notification when carer is available nearby
     */
    public function notifyCarerAvailable(User $client, User $carer): bool
    {
        return $this->sendToUser(
            $client,
            "{$carer->name} is Available Now!",
            "A carer you've messaged before is now available",
            [
                'url' => route('seller.show', ['user' => $carer->id]),
                'tag' => 'carer-available-' . $carer->id
            ]
        );
    }

    /**
     * Check if service is configured
     */
    public function isConfigured(): bool
    {
        return $this->webPush !== null;
    }
}

