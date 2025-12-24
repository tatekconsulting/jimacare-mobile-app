<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Timesheet;
use App\Models\JobApplication;
use App\Models\UserNotification;
use App\Models\Contract;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request){
    	// Get all timesheets with relationships (for dashboard preview)
    	$timesheetsQuery = Timesheet::with(['contract', 'carer', 'client']);
        
        // Order by date if column exists, otherwise use created_at
        $hasDateColumn = Schema::hasColumn('timesheets', 'date');
        if ($hasDateColumn) {
            $timesheetsQuery->orderBy('date', 'desc')->orderBy('clock_in', 'desc');
        } else {
            $timesheetsQuery->orderBy('created_at', 'desc')->orderBy('clock_in', 'desc');
        }
        
        // Apply filters if provided
        if ($request->has('status') && $request->status) {
            $timesheetsQuery->where('status', $request->status);
        }
        
        // Date filters - use date column if exists, otherwise use created_at
        $dateColumn = $hasDateColumn ? 'date' : 'created_at';
        
        if ($request->has('date_from') && $request->date_from) {
            $timesheetsQuery->where($dateColumn, '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $timesheetsQuery->where($dateColumn, '<=', $request->date_to);
        }
        
        $timesheets = $timesheetsQuery->limit(10)->get(); // Show only 10 on dashboard
        
        // ============ COMPREHENSIVE STATISTICS ============
        
        // Timesheet Statistics
        $totalTimesheets = Timesheet::count();
        $pendingTimesheets = Timesheet::where('status', 'pending')->whereNotNull('clock_out')->count();
        $approvedTimesheets = Timesheet::where('status', 'approved')->count();
        $disputedTimesheets = Timesheet::where('status', 'disputed')->count();
        $cancelledTimesheets = Timesheet::where('status', 'cancelled')->count();
        $totalAmount = Timesheet::where('status', 'approved')->sum('total_amount');
        
        // Use date column if exists, otherwise use created_at
        $dateColumn = $hasDateColumn ? 'date' : 'created_at';
        $todayTimesheets = Timesheet::whereDate($dateColumn, today())->count();
        $thisMonthAmount = Timesheet::where('status', 'approved')
            ->whereMonth($dateColumn, now()->month)
            ->whereYear($dateColumn, now()->year)
            ->sum('total_amount');
        
        // User Statistics
        $totalUsers = User::count();
        $totalCarers = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['carer', 'childminder', 'housekeeper']);
        })->count();
        
        // Breakdown by service type
        $totalCarersOnly = User::whereHas('role', function($q) {
            $q->where('slug', 'carer');
        })->count();
        $totalChildminders = User::whereHas('role', function($q) {
            $q->where('slug', 'childminder');
        })->count();
        $totalHousekeepers = User::whereHas('role', function($q) {
            $q->where('slug', 'housekeeper');
        })->count();
        
        $totalClients = User::whereHas('role', function($q) {
            $q->where('slug', 'client');
        })->count();
        $activeUsers = User::where('status', 'active')->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Job Statistics
        $totalJobs = Contract::count();
        $activeJobs = Contract::where('status', 'active')->count();
        $pendingJobs = Contract::where('status', 'pending')->count();
        $jobsThisMonth = Contract::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Jobs by service type (using role_id: 3=carer, 4=childminder, 5=housekeeper)
        $jobsForCarers = Contract::where('role_id', 3)->count();
        $jobsForChildminders = Contract::where('role_id', 4)->count();
        $jobsForHousekeepers = Contract::where('role_id', 5)->count();
        
        // Application Statistics
        $totalApplications = JobApplication::count();
        $pendingApplications = JobApplication::where('status', 'pending')->count();
        $acceptedApplications = JobApplication::where('status', 'accepted')->count();
        $applicationsThisWeek = JobApplication::where('created_at', '>=', now()->subWeek())->count();
        
        // Notification Statistics
        $totalNotifications = UserNotification::count();
        $unreadNotifications = UserNotification::where('is_read', false)->count();
        $notificationsToday = UserNotification::whereDate('created_at', today())->count();
        
        // Recent activity
        $recentApplications = JobApplication::with(['contract', 'carer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentJobs = Contract::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
    	return view('admin.pages.index', compact(
            'timesheets',
            // Timesheet stats
            'totalTimesheets',
            'pendingTimesheets',
            'approvedTimesheets',
            'totalAmount',
            'todayTimesheets',
            'thisMonthAmount',
            // User stats
            'totalUsers',
            'totalCarers',
            'totalCarersOnly',
            'totalChildminders',
            'totalHousekeepers',
            'totalClients',
            'activeUsers',
            // Job stats
            'activeJobs',
            'jobsForCarers',
            'jobsForChildminders',
            'jobsForHousekeepers',
            // Application stats
            'totalApplications',
            'pendingApplications',
            'acceptedApplications',
            'applicationsThisWeek',
            // Notification stats
            'unreadNotifications',
            'notificationsToday',
            // Recent activity
            'recentApplications',
            'recentJobs'
        ));
    }
}
