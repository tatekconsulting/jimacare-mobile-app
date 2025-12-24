<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Contract;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AnalyticsApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get comprehensive dashboard analytics
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $period = $request->input('period', 'month'); // week, month, year, all

        $startDate = $this->getStartDate($period);

        if (in_array($user->role_id, [3, 4, 5])) {
            // Carer dashboard
            return $this->carerDashboard($user, $startDate, $period);
        } else {
            // Client dashboard
            return $this->clientDashboard($user, $startDate, $period);
        }
    }

    /**
     * Carer-specific dashboard
     */
    private function carerDashboard(User $user, $startDate, $period)
    {
        // Earnings data
        $earnings = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('price');

        $previousEarnings = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $this->getStartDate($period, true),
                $startDate
            ])
            ->sum('price');

        $earningsChange = $previousEarnings > 0 
            ? round((($earnings - $previousEarnings) / $previousEarnings) * 100, 1)
            : 0;

        // Bookings data
        $totalBookings = Order::where('seller_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $completedBookings = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->count();

        // Reviews data
        $reviews = Review::where('seller_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->get();

        $avgRating = $reviews->avg('stars') ?? 0;
        $totalReviews = $reviews->count();

        // Response rate (messages responded within 1 hour)
        $responseRate = $this->calculateResponseRate($user, $startDate);

        // Profile views (you'd need to track this separately)
        $profileViews = DB::table('profile_views')
            ->where('viewed_user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        // Earnings chart data
        $earningsChart = $this->getEarningsChartData($user->id, $startDate, $period);

        // Top clients
        $topClients = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select('client_id', DB::raw('COUNT(*) as bookings'), DB::raw('SUM(price) as total_spent'))
            ->groupBy('client_id')
            ->orderByDesc('bookings')
            ->limit(5)
            ->with('client:id,firstname,lastname,profile')
            ->get();

        // Busy hours analysis
        $busyHours = $this->getBusyHoursAnalysis($user->id, $startDate);

        return response()->json([
            'success' => true,
            'period' => $period,
            'summary' => [
                'earnings' => [
                    'current' => round($earnings, 2),
                    'previous' => round($previousEarnings, 2),
                    'change_percent' => $earningsChange,
                    'currency' => 'GBP'
                ],
                'bookings' => [
                    'total' => $totalBookings,
                    'completed' => $completedBookings,
                    'completion_rate' => $totalBookings > 0 
                        ? round(($completedBookings / $totalBookings) * 100, 1) 
                        : 0
                ],
                'reviews' => [
                    'average_rating' => round($avgRating, 1),
                    'total_reviews' => $totalReviews,
                    'recent_reviews' => $reviews->take(3)->map(fn($r) => [
                        'stars' => $r->stars,
                        'comment' => $r->comment,
                        'date' => $r->created_at->format('M j, Y')
                    ])
                ],
                'performance' => [
                    'response_rate' => $responseRate,
                    'profile_views' => $profileViews,
                    'repeat_client_rate' => $this->getRepeatClientRate($user->id, $startDate)
                ]
            ],
            'charts' => [
                'earnings' => $earningsChart,
                'busy_hours' => $busyHours
            ],
            'top_clients' => $topClients,
            'insights' => $this->generateCarerInsights($user, $earnings, $avgRating, $responseRate)
        ]);
    }

    /**
     * Client-specific dashboard
     */
    private function clientDashboard(User $user, $startDate, $period)
    {
        // Spending data
        $spending = Order::where('client_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('price');

        // Bookings
        $totalBookings = Order::where('client_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        // Favorite carers
        $favoriteCarers = Order::where('client_id', $user->id)
            ->where('status', 'completed')
            ->select('seller_id', DB::raw('COUNT(*) as bookings'))
            ->groupBy('seller_id')
            ->orderByDesc('bookings')
            ->limit(5)
            ->with('seller:id,firstname,lastname,profile,fee')
            ->get();

        // Reviews given
        $reviewsGiven = Review::whereHas('order', function ($q) use ($user) {
            $q->where('client_id', $user->id);
        })->where('created_at', '>=', $startDate)->count();

        // Spending by category
        $spendingByCategory = Order::where('client_id', $user->id)
            ->where('status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->join('users', 'orders.seller_id', '=', 'users.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('roles.title as category', DB::raw('SUM(orders.price) as total'))
            ->groupBy('roles.title')
            ->get();

        return response()->json([
            'success' => true,
            'period' => $period,
            'summary' => [
                'spending' => [
                    'total' => round($spending, 2),
                    'currency' => 'GBP'
                ],
                'bookings' => [
                    'total' => $totalBookings
                ],
                'reviews_given' => $reviewsGiven
            ],
            'favorite_carers' => $favoriteCarers,
            'spending_by_category' => $spendingByCategory,
            'recent_bookings' => Order::where('client_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->with('seller:id,firstname,lastname,profile')
                ->get()
        ]);
    }

    /**
     * Get detailed earnings breakdown
     */
    public function earnings(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role_id, [3, 4, 5])) {
            return response()->json([
                'success' => false,
                'message' => 'Earnings data only available for service providers'
            ], 403);
        }

        $year = $request->input('year', date('Y'));

        $monthlyEarnings = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(price) as earnings'),
                DB::raw('COUNT(*) as bookings')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        $totalEarnings = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->sum('price');

        $totalBookings = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->count();

        return response()->json([
            'success' => true,
            'year' => $year,
            'total_earnings' => round($totalEarnings, 2),
            'total_bookings' => $totalBookings,
            'average_per_booking' => $totalBookings > 0 
                ? round($totalEarnings / $totalBookings, 2) 
                : 0,
            'monthly_breakdown' => $monthlyEarnings,
            'currency' => 'GBP'
        ]);
    }

    /**
     * Get performance metrics
     */
    public function performance(Request $request)
    {
        $user = auth()->user();
        $startDate = $this->getStartDate($request->input('period', 'month'));

        $metrics = [
            'response_time' => $this->calculateAverageResponseTime($user, $startDate),
            'response_rate' => $this->calculateResponseRate($user, $startDate),
            'completion_rate' => $this->calculateCompletionRate($user, $startDate),
            'rating_trend' => $this->getRatingTrend($user, $startDate),
            'booking_acceptance_rate' => $this->getBookingAcceptanceRate($user, $startDate)
        ];

        return response()->json([
            'success' => true,
            'metrics' => $metrics,
            'benchmarks' => [
                'response_rate' => 90,
                'completion_rate' => 95,
                'min_rating' => 4.5
            ]
        ]);
    }

    // Helper methods
    private function getStartDate($period, $previous = false)
    {
        $base = $previous ? now()->sub($period, 1) : now();

        return match($period) {
            'week' => $base->startOfWeek(),
            'month' => $base->startOfMonth(),
            'year' => $base->startOfYear(),
            default => Carbon::createFromTimestamp(0)
        };
    }

    private function calculateResponseRate($user, $startDate)
    {
        // This would need actual message tracking
        return 92; // Placeholder
    }

    private function calculateAverageResponseTime($user, $startDate)
    {
        return 15; // Placeholder: 15 minutes average
    }

    private function calculateCompletionRate($user, $startDate)
    {
        $total = Order::where('seller_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $completed = Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->count();

        return $total > 0 ? round(($completed / $total) * 100, 1) : 100;
    }

    private function getRepeatClientRate($userId, $startDate)
    {
        $clients = Order::where('seller_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->distinct('client_id')
            ->count('client_id');

        $repeatClients = Order::where('seller_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->select('client_id')
            ->groupBy('client_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        return $clients > 0 ? round(($repeatClients / $clients) * 100, 1) : 0;
    }

    private function getEarningsChartData($userId, $startDate, $period)
    {
        $groupBy = $period === 'week' ? 'DATE(created_at)' : 'DATE_FORMAT(created_at, "%Y-%m")';

        return Order::where('seller_id', $userId)
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw("$groupBy as date"),
                DB::raw('SUM(price) as earnings')
            )
            ->groupBy(DB::raw($groupBy))
            ->orderBy('date')
            ->get();
    }

    private function getBusyHoursAnalysis($userId, $startDate)
    {
        // Placeholder - would need booking time data
        return [
            ['hour' => '09:00', 'bookings' => 5],
            ['hour' => '10:00', 'bookings' => 8],
            ['hour' => '14:00', 'bookings' => 12],
            ['hour' => '16:00', 'bookings' => 10],
            ['hour' => '18:00', 'bookings' => 7]
        ];
    }

    private function getRatingTrend($user, $startDate)
    {
        return Review::where('seller_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('AVG(stars) as avg_rating')
            )
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('month')
            ->get();
    }

    private function getBookingAcceptanceRate($user, $startDate)
    {
        return 95; // Placeholder
    }

    private function generateCarerInsights($user, $earnings, $avgRating, $responseRate)
    {
        $insights = [];

        if ($responseRate < 80) {
            $insights[] = [
                'type' => 'warning',
                'icon' => '⚡',
                'message' => 'Your response rate is below average. Try to respond to messages within 1 hour to improve bookings.'
            ];
        }

        if ($avgRating >= 4.8) {
            $insights[] = [
                'type' => 'success',
                'icon' => '⭐',
                'message' => 'Excellent rating! You\'re among the top-rated carers.'
            ];
        }

        if ($user->fee && $user->fee < 12) {
            $insights[] = [
                'type' => 'info',
                'icon' => '💡',
                'message' => 'Based on your ratings, you could increase your hourly rate by £2-3.'
            ];
        }

        if (!$user->available_now) {
            $insights[] = [
                'type' => 'tip',
                'icon' => '🟢',
                'message' => 'Set yourself as "Available Now" to get more instant bookings.'
            ];
        }

        return $insights;
    }
}

