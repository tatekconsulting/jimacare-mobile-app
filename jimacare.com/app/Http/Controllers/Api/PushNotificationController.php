<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Subscribe to push notifications
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string'
        ]);

        $user = auth()->user();
        $subscription = json_encode($request->all());

        $user->push_subscription = $subscription;
        $user->save();

        Log::info('Push subscription saved for user: ' . $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Push notifications enabled'
        ]);
    }

    /**
     * Unsubscribe from push notifications
     */
    public function unsubscribe()
    {
        $user = auth()->user();
        $user->push_subscription = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Push notifications disabled'
        ]);
    }

    /**
     * Get subscription status
     */
    public function status()
    {
        $user = auth()->user();

        return response()->json([
            'subscribed' => !empty($user->push_subscription)
        ]);
    }

    /**
     * Send test notification
     */
    public function test()
    {
        $user = auth()->user();

        if (empty($user->push_subscription)) {
            return response()->json([
                'success' => false,
                'message' => 'No push subscription found'
            ], 400);
        }

        $sent = app(\App\Services\PushNotificationService::class)->sendToUser(
            $user,
            'Test Notification',
            'Push notifications are working! 🎉',
            ['url' => route('profile')]
        );

        return response()->json([
            'success' => $sent,
            'message' => $sent ? 'Test notification sent' : 'Failed to send notification'
        ]);
    }
}

