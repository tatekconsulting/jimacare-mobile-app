<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Timesheet;
use App\Models\Contract;
use App\Models\User;

class TimesheetController extends Controller
{
    /**
     * Display all timesheets with filters
     */
    public function index(Request $request)
    {
        $query = Timesheet::with(['contract', 'carer', 'client']);
        
        // Check if date column exists
        $hasDateColumn = Schema::hasColumn('timesheets', 'date');
        $dateColumn = $hasDateColumn ? 'date' : 'created_at';
        
        // Order by date if column exists, otherwise use created_at
        if ($hasDateColumn) {
            $query->orderBy('date', 'desc')->orderBy('clock_in', 'desc');
        } else {
            $query->orderBy('created_at', 'desc')->orderBy('clock_in', 'desc');
        }
        
        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->where($dateColumn, '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->where($dateColumn, '<=', $request->date_to);
        }
        
        if ($request->has('carer_id') && $request->carer_id) {
            $query->where('carer_id', $request->carer_id);
        }
        
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }
        
        // Filter by service type (carer_type: 'carer', 'childminder', 'housekeeper')
        if ($request->has('carer_type') && $request->carer_type) {
            $query->whereHas('carer', function($q) use ($request) {
                $q->whereHas('role', function($r) use ($request) {
                    $r->where('slug', $request->carer_type);
                });
            });
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('carer', function($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('client', function($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('contract', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            });
        }
        
        $timesheets = $query->paginate(25);
        
        // Statistics
        $totalTimesheets = Timesheet::count();
        $pendingTimesheets = Timesheet::where('status', 'pending')->whereNotNull('clock_out')->count();
        $approvedTimesheets = Timesheet::where('status', 'approved')->count();
        $disputedTimesheets = Timesheet::where('status', 'disputed')->count();
        $cancelledTimesheets = Timesheet::where('status', 'cancelled')->count();
        $totalAmount = Timesheet::where('status', 'approved')->sum('total_amount');
        
        // Get carers and clients for filter dropdowns
        $carers = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['carer', 'childminder', 'housekeeper']);
        })->orderBy('firstname')->get();
        
        $clients = User::whereHas('role', function($q) {
            $q->where('slug', 'client');
        })->orderBy('firstname')->get();
        
        return view('admin.pages.timesheets.index', compact(
            'timesheets',
            'totalTimesheets',
            'pendingTimesheets',
            'approvedTimesheets',
            'disputedTimesheets',
            'cancelledTimesheets',
            'totalAmount',
            'carers',
            'clients'
        ));
    }
    
    /**
     * Approve timesheet (admin override)
     */
    public function approve(Request $request, Timesheet $timesheet)
    {
        $timesheet->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'Timesheet #' . $timesheet->id . ' approved successfully.');
    }
    
    /**
     * Dispute timesheet (admin override)
     */
    public function dispute(Request $request, Timesheet $timesheet)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);
        
        $timesheet->update([
            'status' => 'disputed',
            'dispute_reason' => $request->reason,
        ]);
        
        return back()->with('success', 'Timesheet #' . $timesheet->id . ' disputed.');
    }

    /**
     * Cancel timesheet (admin override)
     */
    public function cancel(Request $request, Timesheet $timesheet)
    {
        // Only approved timesheets can be cancelled
        if ($timesheet->status !== 'approved') {
            return back()->with('error', 'Only approved timesheets can be cancelled.');
        }
        
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);
        
        $timesheet->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason,
            'cancelled_by' => auth()->id(),
        ]);
        
        // Notify carer
        \App\Models\UserNotification::create([
            'user_id' => $timesheet->carer_id,
            'type' => 'timesheet_cancelled',
            'title' => 'Timesheet Cancelled',
            'message' => 'Your timesheet for ' . ($timesheet->date ? $timesheet->date->format('Y-m-d') : 'N/A') . ' has been cancelled by admin. Reason: ' . $request->reason,
            'action_url' => '/carer/timesheets',
            'data' => json_encode([
                'timesheet_id' => $timesheet->id,
                'reason' => $request->reason,
            ]),
        ]);
        
        // Notify client
        if ($timesheet->client_id) {
            \App\Models\UserNotification::create([
                'user_id' => $timesheet->client_id,
                'type' => 'timesheet_cancelled',
                'title' => 'Timesheet Cancelled',
                'message' => 'Your timesheet for ' . ($timesheet->date ? $timesheet->date->format('Y-m-d') : 'N/A') . ' has been cancelled by admin. Reason: ' . $request->reason,
                'action_url' => '/client/timesheets',
                'data' => json_encode([
                    'timesheet_id' => $timesheet->id,
                    'reason' => $request->reason,
                ]),
            ]);
        }
        
        return back()->with('success', 'Timesheet #' . $timesheet->id . ' cancelled successfully.');
    }

    /**
     * View single timesheet details
     */
    public function show(Timesheet $timesheet)
    {
        $timesheet->load(['contract', 'carer', 'client']);
        return view('admin.pages.timesheets.show', compact('timesheet'));
    }
}

