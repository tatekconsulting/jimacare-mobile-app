<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarerLocation;
use App\Models\User;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update carer's location
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        
        // Update or create location record
        CarerLocation::updateOrCreate(
            ['carer_id' => $user->id],
            [
                'latitude' => $request->lat,
                'longitude' => $request->lng,
                'is_active' => true,
                'last_updated' => now(),
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Location updated',
        ]);
    }

    /**
     * Get carer's location (for clients)
     */
    public function getLocation(User $carer)
    {
        $location = CarerLocation::where('carer_id', $carer->id)
            ->where('is_active', true)
            ->first();
        
        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not available',
            ], 404);
        }
        
        // Check if location is recent (within last 5 minutes)
        $isRecent = $location->last_updated && $location->last_updated->gt(now()->subMinutes(5));
        
        return response()->json([
            'success' => true,
            'location' => [
                'lat' => $location->latitude,
                'lng' => $location->longitude,
                'last_updated' => $location->last_updated->toIso8601String(),
                'is_recent' => $isRecent,
            ],
        ]);
    }
}

