<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Role;
use App\Models\Day;
use App\Models\Experience;
use App\Services\CarerMatchingService;

class ChatbotJobController extends Controller
{
    protected $matchingService;

    public function __construct(CarerMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    public function getJobTypes()
    {
        $roles = Role::where('id', '>', 2)->get(['id', 'title', 'slug']);
        return response()->json(['success' => true, 'data' => $roles]);
    }

    public function getDays()
    {
        $days = Day::all(['id', 'title']);
        return response()->json(['success' => true, 'data' => $days]);
    }

    public function getExperiences(Request $request)
    {
        $roleId = $request->get('role_id');
        $experiences = Experience::where('role_id', $roleId)->get(['id', 'title']);
        return response()->json(['success' => true, 'data' => $experiences]);
    }

    public function postJob(Request $request)
    {
        // SECURITY: Check authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to post a job',
                'redirect' => '/login'
            ], 401);
        }

        $user = auth()->user();
        
        // SECURITY: Only clients (role_id = 2) and admins (role_id = 1) can post jobs
        if (!in_array($user->role_id, [1, 2])) {
            return response()->json([
                'success' => false,
                'message' => 'Only clients can post jobs',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
                'description' => 'required|string|min:10',
                'days' => 'nullable|array',
                'start_time' => 'nullable|string',
                'end_time' => 'nullable|string',
                'address' => 'nullable|string',
                'lat' => 'nullable|numeric',
                'long' => 'nullable|numeric',
                'radius' => 'nullable|integer|min:1|max:100', // Add radius validation
                'skills' => 'nullable|array',
            ]);

            // Set default radius to 10 miles if not provided (for location-based notifications)
            $radius = $validated['radius'] ?? 10;

            $contract = Contract::create([
                'user_id' => $user->id,
                'type_id' => $validated['role_id'],
                'desc' => $validated['description'],
                'address' => $validated['address'] ?? $user->address ?? '',
                'lat' => $validated['lat'] ?? $user->lat ?? null,
                'long' => $validated['long'] ?? $user->long ?? null,
                'radius' => $radius, // Set radius for location-based email notifications
                'status' => 'active',
            ]);

            $matches = $this->matchingService->findMatches([
                'role_id' => $validated['role_id'],
                'description' => $validated['description'],
                'days' => $validated['days'] ?? [],
                'skills' => $validated['skills'] ?? [],
                'lat' => $validated['lat'] ?? $user->lat ?? null,
                'long' => $validated['long'] ?? $user->long ?? null,
            ], 5);

            $formattedMatches = $this->formatMatches($matches);

            return response()->json([
                'success' => true,
                'message' => 'Job posted successfully!',
                'job_id' => $contract->id,
                'matches' => $formattedMatches,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error posting job: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getRecommendations(Request $request)
    {
        try {
            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
                'description' => 'nullable|string',
                'days' => 'nullable|array',
                'lat' => 'nullable|numeric',
                'long' => 'nullable|numeric',
                'skills' => 'nullable|array',
            ]);

            $matches = $this->matchingService->findMatches($validated, 5);
            $formattedMatches = $this->formatMatches($matches);

            return response()->json([
                'success' => true,
                'matches' => $formattedMatches,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error finding matches: ' . $e->getMessage(),
                'matches' => [],
            ], 500);
        }
    }

    private function formatMatches($matches)
    {
        return $matches->map(function ($match) {
            $carer = $match['carer'];
            return [
                'id' => $carer->id,
                'name' => $carer->firstname . ' ' . substr($carer->lastname ?? '', 0, 1) . '.',
                'profile' => asset($carer->profile ?? 'img/undraw_profile.svg'),
                'score' => $match['total_score'],
                'reasons' => $match['match_reasons'],
                'city' => $carer->city ?? '',
                'reviews_count' => $carer->reviews->count(),
                'reviews_avg' => round($carer->reviews->avg('stars') ?? 0, 1),
                'verified' => $carer->approved ?? false,
                'url' => '/profile/' . $carer->id,
            ];
        })->toArray();
    }
}
