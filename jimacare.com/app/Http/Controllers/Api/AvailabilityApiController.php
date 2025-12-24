<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\AvailabilityChangedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AvailabilityApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Toggle available now status
     */
    public function toggle(Request $request)
    {
        $user = auth()->user();

        // Only allow sellers to toggle availability
        if ($user->role_id === 2) {
            return response()->json([
                'success' => false,
                'message' => 'Only service providers can set availability'
            ], 403);
        }

        $request->validate([
            'available' => 'required|boolean',
            'duration' => 'nullable|integer|min:1|max:24' // hours
        ]);

        $available = $request->boolean('available');
        $duration = $request->input('duration', 4); // Default 4 hours

        $user->available_now = $available;
        $user->available_until = $available ? Carbon::now()->addHours($duration) : null;
        $user->save();

        // Broadcast availability change
        if (class_exists('App\Events\AvailabilityChangedEvent')) {
            broadcast(new AvailabilityChangedEvent($user));
        }

        return response()->json([
            'success' => true,
            'available_now' => $user->available_now,
            'available_until' => $user->available_until?->format('Y-m-d H:i:s'),
            'message' => $available 
                ? "You're now visible as available for the next {$duration} hours!" 
                : "You're no longer shown as available"
        ]);
    }

    /**
     * Get current availability status
     */
    public function status()
    {
        $user = auth()->user();

        // Check if availability has expired
        if ($user->available_now && $user->available_until && Carbon::now()->isAfter($user->available_until)) {
            $user->available_now = false;
            $user->available_until = null;
            $user->save();
        }

        return response()->json([
            'available_now' => $user->available_now,
            'available_until' => $user->available_until?->format('Y-m-d H:i:s'),
            'remaining_minutes' => $user->available_until 
                ? max(0, Carbon::now()->diffInMinutes($user->available_until, false)) 
                : 0
        ]);
    }

    /**
     * Get available carers near location
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:100', // km
            'type' => 'nullable|integer|exists:roles,id'
        ]);

        $lat = $request->lat;
        $long = $request->long;
        $radius = $request->input('radius', 25); // Default 25km
        $type = $request->input('type');

        $query = \App\Models\User::query()
            ->where('available_now', true)
            ->where('status', 'active')
            ->whereNotNull('lat')
            ->whereNotNull('long')
            ->where(function ($q) {
                $q->whereNull('available_until')
                  ->orWhere('available_until', '>', now());
            });

        if ($type) {
            $query->where('role_id', $type);
        } else {
            $query->whereIn('role_id', [3, 4, 5]); // Sellers only
        }

        // Haversine formula for distance calculation
        $query->selectRaw("
            *,
            (6371 * acos(
                cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) +
                sin(radians(?)) * sin(radians(lat))
            )) AS distance
        ", [$lat, $long, $lat])
        ->having('distance', '<=', $radius)
        ->orderBy('distance');

        $carers = $query->limit(20)->get();

        return response()->json([
            'success' => true,
            'carers' => $carers->map(function ($carer) {
                return [
                    'id' => $carer->id,
                    'name' => $carer->name,
                    'profile' => asset($carer->profile ?? 'img/undraw_profile.svg'),
                    'role' => $carer->role->title ?? '',
                    'rating' => round($carer->reviews_avg ?? 0, 1),
                    'reviews_count' => $carer->reviews_count ?? 0,
                    'distance' => round($carer->distance, 1),
                    'fee' => $carer->fee,
                    'available_until' => $carer->available_until?->format('H:i'),
                    'url' => route('seller.show', ['user' => $carer->id])
                ];
            }),
            'count' => $carers->count()
        ]);
    }
}

