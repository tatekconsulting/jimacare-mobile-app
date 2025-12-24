<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchApiController extends Controller
{
    /**
     * Search carers with advanced filters
     */
    public function carers(Request $request)
    {
        // Require authentication
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to search for carers.'
            ], 401);
        }

        $user = auth()->user();
        $userRole = $user->role->slug ?? '';
        $isAdmin = $user->role_id == 1 || $userRole === 'admin';
        $isClient = $userRole === 'client';
        $isServiceProvider = in_array($userRole, ['carer', 'childminder', 'housekeeper']);

        // Only Clients and Admins can search for carers
        if (!$isAdmin && !$isClient) {
            $roleTitle = $user->role->title ?? 'Service Provider';
            return response()->json([
                'success' => false,
                'message' => "As a {$roleTitle}, you can browse available jobs instead. Only clients can search for service providers.",
                'redirect' => route('contract.index')
            ], 403);
        }

        $request->validate([
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
            'type' => 'nullable|integer|exists:roles,id',
            'available_now' => 'nullable|boolean',
            'min_rating' => 'nullable|numeric|min:0|max:5',
            'max_rate' => 'nullable|numeric|min:0',
            'experience' => 'nullable|array',
            'experience.*' => 'integer|exists:experiences,id',
            'languages' => 'nullable|array',
            'languages.*' => 'integer|exists:languages,id',
            'skills' => 'nullable|array',
            'skills.*' => 'integer|exists:skills,id',
            'has_dbs' => 'nullable|boolean',
            'has_vehicle' => 'nullable|boolean',
            'verified_only' => 'nullable|boolean',
            'instant_book' => 'nullable|boolean',
            'sort' => 'nullable|string|in:distance,rating,price_low,price_high,reviews',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        $query = User::query()
            ->where('status', 'active')
            ->whereIn('role_id', [3, 4, 5]); // Only service providers

        // Filter out users of the same role as the logged-in user (if they're a service provider)
        // This shouldn't happen since only clients/admins can access, but adding for safety
        if ($isServiceProvider && !$isAdmin) {
            $query->where('role_id', '!=', $user->role_id);
        }

        // Filter by role type
        if ($request->type) {
            $query->where('role_id', $request->type);
        }

        // Available now filter
        if ($request->boolean('available_now')) {
            $query->where('available_now', true)
                ->where(function ($q) {
                    $q->whereNull('available_until')
                      ->orWhere('available_until', '>', now());
                });
        }

        // Location-based search
        if ($request->lat && $request->long) {
            $lat = $request->lat;
            $long = $request->long;
            $radius = $request->input('radius', 25);

            $query->whereNotNull('lat')
                ->whereNotNull('long')
                ->selectRaw("
                    users.*,
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) +
                        sin(radians(?)) * sin(radians(lat))
                    )) AS distance
                ", [$lat, $long, $lat])
                ->having('distance', '<=', $radius);
        }

        // Minimum rating filter
        if ($request->min_rating) {
            $query->whereHas('reviews', function ($q) {}, '>=', 1)
                ->withAvg('reviews', 'stars')
                ->having('reviews_avg_stars', '>=', $request->min_rating);
        }

        // Maximum rate filter
        if ($request->max_rate) {
            $query->where('fee', '<=', $request->max_rate);
        }

        // Experience filter
        if ($request->experience && count($request->experience) > 0) {
            $query->whereHas('experiences', function ($q) use ($request) {
                $q->whereIn('experiences.id', $request->experience);
            });
        }

        // Languages filter
        if ($request->languages && count($request->languages) > 0) {
            $query->whereHas('languages', function ($q) use ($request) {
                $q->whereIn('languages.id', $request->languages);
            });
        }

        // Skills filter
        if ($request->skills && count($request->skills) > 0) {
            $query->whereHas('skills', function ($q) use ($request) {
                $q->whereIn('skills.id', $request->skills);
            });
        }

        // DBS checked filter
        if ($request->boolean('has_dbs')) {
            $query->whereNotNull('dbs')
                ->where('dbs', '!=', '');
        }

        // Has vehicle filter (for babysitters)
        if ($request->boolean('has_vehicle')) {
            $query->where('drive', true);
        }

        // Verified only filter
        if ($request->boolean('verified_only')) {
            $query->where('approved', true)
                ->whereNotNull('email_verified_at')
                ->whereNotNull('phone_verified_at');
        }

        // Instant book filter (future feature)
        if ($request->boolean('instant_book')) {
            $query->where('instant_book_enabled', true);
        }

        // Sorting
        switch ($request->input('sort', 'distance')) {
            case 'rating':
                $query->withAvg('reviews', 'stars')
                    ->orderByDesc('reviews_avg_stars');
                break;
            case 'price_low':
                $query->orderBy('fee', 'asc');
                break;
            case 'price_high':
                $query->orderBy('fee', 'desc');
                break;
            case 'reviews':
                $query->withCount('reviews')
                    ->orderByDesc('reviews_count');
                break;
            case 'distance':
            default:
                if ($request->lat && $request->long) {
                    $query->orderBy('distance', 'asc');
                } else {
                    $query->orderBy('created_at', 'desc');
                }
                break;
        }

        $perPage = $request->input('per_page', 12);
        $carers = $query->with(['role', 'languages', 'skills', 'experiences'])
            ->withAvg('reviews', 'stars')
            ->withCount('reviews')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $carers->items(),
            'meta' => [
                'current_page' => $carers->currentPage(),
                'last_page' => $carers->lastPage(),
                'per_page' => $carers->perPage(),
                'total' => $carers->total()
            ],
            'filters_applied' => array_filter([
                'type' => $request->type,
                'available_now' => $request->boolean('available_now'),
                'radius' => $request->radius,
                'min_rating' => $request->min_rating,
                'max_rate' => $request->max_rate
            ])
        ]);
    }

    /**
     * Search jobs with advanced filters
     */
    public function jobs(Request $request)
    {
        $request->validate([
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'radius' => 'nullable|numeric|min:1|max:100',
            'type' => 'nullable|integer|exists:roles,id',
            'min_rate' => 'nullable|numeric|min:0',
            'max_rate' => 'nullable|numeric|min:0',
            'experience' => 'nullable|array',
            'start_type' => 'nullable|string|in:immediately,not-sure,specific-date',
            'posted_within' => 'nullable|integer|min:1', // days
            'sort' => 'nullable|string|in:newest,rate_high,rate_low,start_date',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        // Require authentication for job search
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to search for jobs.'
            ], 401);
        }

        $user = auth()->user();
        $userRole = $user->role->slug ?? '';
        $isAdmin = $user->role_id == 1 || $userRole === 'admin';
        $isClient = $userRole === 'client';
        $isCarer = in_array($userRole, ['carer', 'childminder', 'housekeeper']);

        $query = Contract::query()
            ->where('status', 'active')
            ->whereNull('deleted_at');

        // Apply role-based filtering
        if ($isClient) {
            // Clients only see their own jobs
            $query->where('user_id', $user->id);
        } elseif ($isCarer && !$isAdmin) {
            // Carers: Hide jobs that have been filled
            $query->whereNull('filled_at');
        }
        // Admins see all jobs (no filtering)

        // Filter by role type
        if ($request->type) {
            $query->where('role_id', $request->type);
        }

        // Location-based search
        if ($request->lat && $request->long) {
            $lat = $request->lat;
            $long = $request->long;
            $radius = $request->input('radius', 25);

            $query->whereNotNull('lat')
                ->whereNotNull('long')
                ->selectRaw("
                    contracts.*,
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) +
                        sin(radians(?)) * sin(radians(lat))
                    )) AS distance
                ", [$lat, $long, $lat])
                ->having('distance', '<=', $radius);
        }

        // Rate filters
        if ($request->min_rate) {
            $query->where(function ($q) use ($request) {
                $q->where('hourly_rate', '>=', $request->min_rate)
                  ->orWhere('daily_rate', '>=', $request->min_rate);
            });
        }

        if ($request->max_rate) {
            $query->where(function ($q) use ($request) {
                $q->where('hourly_rate', '<=', $request->max_rate)
                  ->orWhere('daily_rate', '<=', $request->max_rate);
            });
        }

        // Experience filter
        if ($request->experience && count($request->experience) > 0) {
            $query->whereHas('experiences', function ($q) use ($request) {
                $q->whereIn('experiences.id', $request->experience);
            });
        }

        // Start type filter
        if ($request->start_type) {
            $query->where('start_type', $request->start_type);
        }

        // Posted within X days
        if ($request->posted_within) {
            $query->where('created_at', '>=', now()->subDays($request->posted_within));
        }

        // Sorting
        switch ($request->input('sort', 'newest')) {
            case 'rate_high':
                $query->orderByRaw('COALESCE(hourly_rate, daily_rate, weekly_rate) DESC');
                break;
            case 'rate_low':
                $query->orderByRaw('COALESCE(hourly_rate, daily_rate, weekly_rate) ASC');
                break;
            case 'start_date':
                $query->orderBy('start_date', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = $request->input('per_page', 12);
        $jobs = $query->with(['user', 'role', 'experiences', 'languages'])
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $jobs->items(),
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total()
            ]
        ]);
    }

    /**
     * Get filter options (for building filter UI)
     */
    public function filterOptions(Request $request)
    {
        $roleId = $request->input('type');

        return response()->json([
            'roles' => \App\Models\Role::where('seller', true)->where('active', true)->get(['id', 'title', 'slug']),
            'experiences' => \App\Models\Experience::when($roleId, fn($q) => $q->where('role_id', $roleId))->get(['id', 'title']),
            'languages' => \App\Models\Language::all(['id', 'title']),
            'skills' => \App\Models\Skill::when($roleId, fn($q) => $q->where('role_id', $roleId))->get(['id', 'title']),
            'sort_options' => [
                ['value' => 'distance', 'label' => 'Nearest First'],
                ['value' => 'rating', 'label' => 'Highest Rated'],
                ['value' => 'price_low', 'label' => 'Price: Low to High'],
                ['value' => 'price_high', 'label' => 'Price: High to Low'],
                ['value' => 'reviews', 'label' => 'Most Reviews']
            ]
        ]);
    }
}

