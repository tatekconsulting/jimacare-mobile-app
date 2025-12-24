<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\LocationUpdatedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update location during active booking
     */
    public function update(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer|exists:instant_bookings,id',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'eta_minutes' => 'nullable|integer|min:0'
        ]);

        $user = auth()->user();

        // Verify user is part of this booking
        $booking = DB::table('instant_bookings')
            ->where('id', $request->booking_id)
            ->where('status', 'accepted')
            ->where(function ($q) use ($user) {
                $q->where('carer_id', $user->id)
                  ->orWhere('client_id', $user->id);
            })
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Active booking not found'
            ], 404);
        }

        // Store location update
        $locationId = DB::table('location_tracks')->insertGetId([
            'booking_id' => $request->booking_id,
            'user_id' => $user->id,
            'lat' => $request->lat,
            'long' => $request->long,
            'accuracy' => $request->accuracy,
            'eta_minutes' => $request->eta_minutes,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Broadcast location update to the other party
        $recipientId = $booking->carer_id === $user->id 
            ? $booking->client_id 
            : $booking->carer_id;

        broadcast(new LocationUpdatedEvent(
            $request->booking_id,
            $user->id,
            $recipientId,
            $request->lat,
            $request->long,
            $request->eta_minutes
        ));

        return response()->json([
            'success' => true,
            'location_id' => $locationId
        ]);
    }

    /**
     * Get location tracking data for a booking
     */
    public function track(Request $request, $bookingId)
    {
        $user = auth()->user();

        // Verify user is part of this booking
        $booking = DB::table('instant_bookings')
            ->where('id', $bookingId)
            ->where(function ($q) use ($user) {
                $q->where('carer_id', $user->id)
                  ->orWhere('client_id', $user->id);
            })
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        // Get the other party's latest location
        $otherUserId = $booking->carer_id === $user->id 
            ? $booking->client_id 
            : $booking->carer_id;

        $latestLocation = DB::table('location_tracks')
            ->where('booking_id', $bookingId)
            ->where('user_id', $otherUserId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Get location history (last 30 minutes)
        $history = DB::table('location_tracks')
            ->where('booking_id', $bookingId)
            ->where('user_id', $otherUserId)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->orderBy('created_at', 'asc')
            ->get(['lat', 'long', 'eta_minutes', 'created_at']);

        $otherUser = \App\Models\User::find($otherUserId);

        return response()->json([
            'success' => true,
            'tracking' => [
                'user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'profile' => asset($otherUser->profile ?? 'img/undraw_profile.svg'),
                    'phone' => $otherUser->phone
                ],
                'current_location' => $latestLocation ? [
                    'lat' => $latestLocation->lat,
                    'long' => $latestLocation->long,
                    'eta_minutes' => $latestLocation->eta_minutes,
                    'updated_at' => $latestLocation->created_at
                ] : null,
                'history' => $history,
                'booking' => [
                    'id' => $booking->id,
                    'date' => $booking->date,
                    'start_time' => $booking->start_time,
                    'status' => $booking->status
                ]
            ]
        ]);
    }

    /**
     * Start location sharing for a booking
     */
    public function startSharing(Request $request, $bookingId)
    {
        $user = auth()->user();

        $booking = DB::table('instant_bookings')
            ->where('id', $bookingId)
            ->where('carer_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Active booking not found'
            ], 404);
        }

        // Mark that location sharing has started
        DB::table('instant_bookings')
            ->where('id', $bookingId)
            ->update([
                'location_sharing_started' => now(),
                'updated_at' => now()
            ]);

        // Notify the client
        $client = \App\Models\User::find($booking->client_id);
        app(\App\Services\PushNotificationService::class)->sendToUser(
            $client,
            '📍 Carer is on the way!',
            "{$user->name} has started sharing their location",
            [
                'url' => route('booking.track', ['booking' => $bookingId]),
                'tag' => 'location-sharing-' . $bookingId
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Location sharing started'
        ]);
    }
}

