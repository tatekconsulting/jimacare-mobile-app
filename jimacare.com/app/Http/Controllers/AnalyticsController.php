<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\JobApplication;
use App\Models\UserNotification;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'month'); // week, month, year, all
        $startDate = $this->getStartDate($period);

        // User Statistics
        $userStats = $this->getUserStatistics($startDate);
        
        // Timesheet Statistics
        $timesheetStats = $this->getTimesheetStatistics($startDate);
        
        // Job Statistics
        $jobStats = $this->getJobStatistics($startDate);
        
        // Application Statistics
        $applicationStats = $this->getApplicationStatistics($startDate);
        
        // Revenue Statistics
        $revenueStats = $this->getRevenueStatistics($startDate);
        
        // Growth Trends
        $growthTrends = $this->getGrowthTrends($period);
        
        // Activity Trends (for charts)
        $activityTrends = $this->getActivityTrends($period);

        return view('app.pages.analytics.index', compact(
            'period',
            'userStats',
            'timesheetStats',
            'jobStats',
            'applicationStats',
            'revenueStats',
            'growthTrends',
            'activityTrends'
        ));
    }

    /**
     * Get start date based on period
     */
    private function getStartDate($period, $previous = false)
    {
        $multiplier = $previous ? 2 : 1;
        
        switch ($period) {
            case 'week':
                return Carbon::now()->subWeeks($multiplier);
            case 'month':
                return Carbon::now()->subMonths($multiplier);
            case 'year':
                return Carbon::now()->subYears($multiplier);
            case 'all':
            default:
                return Carbon::create(2020, 1, 1); // Start of platform
        }
    }

    /**
     * Get user statistics
     */
    private function getUserStatistics($startDate)
    {
        return [
            'total' => User::count(),
            'total_carers' => User::whereHas('role', function($q) {
                $q->where('slug', 'carer');
            })->count(),
            'total_childminders' => User::whereHas('role', function($q) {
                $q->where('slug', 'childminder');
            })->count(),
            'total_housekeepers' => User::whereHas('role', function($q) {
                $q->where('slug', 'housekeeper');
            })->count(),
            'total_clients' => User::whereHas('role', function($q) {
                $q->where('slug', 'client');
            })->count(),
            'active' => User::where('status', 'active')->count(),
            'new_since' => User::where('created_at', '>=', $startDate)->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Get timesheet statistics
     */
    private function getTimesheetStatistics($startDate)
    {
        $hasDateColumn = \Schema::hasColumn('timesheets', 'date');
        $dateColumn = $hasDateColumn ? 'date' : 'created_at';

        return [
            'total' => Timesheet::count(),
            'pending' => Timesheet::where('status', 'pending')->whereNotNull('clock_out')->count(),
            'approved' => Timesheet::where('status', 'approved')->count(),
            'disputed' => Timesheet::where('status', 'disputed')->count(),
            'cancelled' => Timesheet::where('status', 'cancelled')->count(),
            'total_amount' => Timesheet::where('status', 'approved')->sum('total_amount'),
            'since_start' => Timesheet::where($dateColumn, '>=', $startDate)->count(),
            'this_month_amount' => Timesheet::where('status', 'approved')
                ->whereMonth($dateColumn, now()->month)
                ->whereYear($dateColumn, now()->year)
                ->sum('total_amount'),
        ];
    }

    /**
     * Get job statistics
     */
    private function getJobStatistics($startDate)
    {
        return [
            'total' => Contract::count(),
            'active' => Contract::where('status', 'active')->count(),
            'pending' => Contract::where('status', 'pending')->count(),
            'filled' => Contract::whereNotNull('filled_at')->count(),
            'for_carers' => Contract::where('role_id', 3)->count(),
            'for_childminders' => Contract::where('role_id', 4)->count(),
            'for_housekeepers' => Contract::where('role_id', 5)->count(),
            'since_start' => Contract::where('created_at', '>=', $startDate)->count(),
            'this_month' => Contract::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Get application statistics
     */
    private function getApplicationStatistics($startDate)
    {
        return [
            'total' => JobApplication::count(),
            'pending' => JobApplication::where('status', 'pending')->count(),
            'accepted' => JobApplication::where('status', 'accepted')->count(),
            'rejected' => JobApplication::where('status', 'rejected')->count(),
            'acceptance_rate' => JobApplication::count() > 0 
                ? round((JobApplication::where('status', 'accepted')->count() / JobApplication::count()) * 100, 1)
                : 0,
            'since_start' => JobApplication::where('created_at', '>=', $startDate)->count(),
            'this_week' => JobApplication::where('created_at', '>=', Carbon::now()->subWeek())->count(),
        ];
    }

    /**
     * Get revenue statistics
     */
    private function getRevenueStatistics($startDate)
    {
        $hasDateColumn = \Schema::hasColumn('timesheets', 'date');
        $dateColumn = $hasDateColumn ? 'date' : 'created_at';

        $totalRevenue = Timesheet::where('status', 'approved')->sum('total_amount');
        $revenueSince = Timesheet::where('status', 'approved')
            ->where($dateColumn, '>=', $startDate)
            ->sum('total_amount');

        // Revenue by service type
        $carerRevenue = Timesheet::where('status', 'approved')
            ->whereHas('carer.role', function($q) {
                $q->where('slug', 'carer');
            })
            ->sum('total_amount');

        $childminderRevenue = Timesheet::where('status', 'approved')
            ->whereHas('carer.role', function($q) {
                $q->where('slug', 'childminder');
            })
            ->sum('total_amount');

        $housekeeperRevenue = Timesheet::where('status', 'approved')
            ->whereHas('carer.role', function($q) {
                $q->where('slug', 'housekeeper');
            })
            ->sum('total_amount');

        return [
            'total' => $totalRevenue,
            'since_start' => $revenueSince,
            'this_month' => Timesheet::where('status', 'approved')
                ->whereMonth($dateColumn, now()->month)
                ->whereYear($dateColumn, now()->year)
                ->sum('total_amount'),
            'by_service' => [
                'carers' => $carerRevenue,
                'childminders' => $childminderRevenue,
                'housekeepers' => $housekeeperRevenue,
            ],
        ];
    }

    /**
     * Get growth trends
     */
    private function getGrowthTrends($period)
    {
        $currentStart = $this->getStartDate($period);
        $previousStart = $this->getStartDate($period, true);

        $currentUsers = User::where('created_at', '>=', $currentStart)->count();
        $previousUsers = User::whereBetween('created_at', [$previousStart, $currentStart])->count();

        $currentJobs = Contract::where('created_at', '>=', $currentStart)->count();
        $previousJobs = Contract::whereBetween('created_at', [$previousStart, $currentStart])->count();

        return [
            'users' => [
                'current' => $currentUsers,
                'previous' => $previousUsers,
                'change' => $previousUsers > 0 ? round((($currentUsers - $previousUsers) / $previousUsers) * 100, 1) : 0,
            ],
            'jobs' => [
                'current' => $currentJobs,
                'previous' => $previousJobs,
                'change' => $previousJobs > 0 ? round((($currentJobs - $previousJobs) / $previousJobs) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Get activity trends for charts
     */
    private function getActivityTrends($period)
    {
        $data = [];
        $format = 'Y-m-d';
        $interval = 'day';

        if ($period === 'year') {
            $format = 'Y-m';
            $interval = 'month';
            $start = Carbon::now()->subYear()->startOfMonth();
        } elseif ($period === 'month') {
            $start = Carbon::now()->subMonth()->startOfDay();
        } else {
            $start = Carbon::now()->subWeek()->startOfDay();
        }

        $end = Carbon::now();
        $current = $start->copy();

        while ($current <= $end) {
            $dateKey = $current->format($format);
            
            $data[] = [
                'date' => $dateKey,
                'users' => User::whereDate('created_at', $current->format('Y-m-d'))->count(),
                'jobs' => Contract::whereDate('created_at', $current->format('Y-m-d'))->count(),
                'applications' => JobApplication::whereDate('created_at', $current->format('Y-m-d'))->count(),
            ];

            if ($interval === 'month') {
                $current->addMonth();
            } else {
                $current->addDay();
            }
        }

        return $data;
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        // This would generate a CSV/Excel export
        // For now, return JSON
        $period = $request->input('period', 'month');
        $startDate = $this->getStartDate($period);

        return response()->json([
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'user_stats' => $this->getUserStatistics($startDate),
            'timesheet_stats' => $this->getTimesheetStatistics($startDate),
            'job_stats' => $this->getJobStatistics($startDate),
            'application_stats' => $this->getApplicationStatistics($startDate),
            'revenue_stats' => $this->getRevenueStatistics($startDate),
        ]);
    }
}

