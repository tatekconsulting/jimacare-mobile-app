<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Timesheet;
use App\Models\UserNotification;
use App\Models\Contract;
use App\Models\JobApplication;
use App\Services\FaceVerificationService;

class TimesheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Carer/Admin: View their timesheets (or all if admin)
     */
    public function carerIndex()
    {
        $user = auth()->user();
        $isAdmin = $user->role_id == 1 || ($user->role->slug ?? '') === 'admin';
        $hasDateColumn = Schema::hasColumn('timesheets', 'date');
        $orderColumn = $hasDateColumn ? 'date' : 'created_at';
        
        if ($isAdmin) {
            // Admin can see all timesheets
            $acceptedJobs = collect(); // Not needed for admin
            $timesheets = Timesheet::with(['contract', 'client', 'carer'])
                ->orderBy($orderColumn, 'desc')
                ->paginate(20);
            $activeTimesheet = null; // Not needed for admin
        } else {
            // Get accepted applications (active jobs for this carer)
            $acceptedJobs = JobApplication::where('carer_id', $user->id)
                ->where('status', 'accepted')
                ->with('contract')
                ->get();
            
            // Get timesheets
            $timesheets = Timesheet::where('carer_id', $user->id)
                ->with(['contract', 'client'])
                ->orderBy($orderColumn, 'desc')
                ->paginate(20);
            
            // Check if there's an active (clocked-in) timesheet
            $activeTimesheet = Timesheet::where('carer_id', $user->id)
                ->whereNull('clock_out')
                ->first();
        }
        
        return view('app.pages.carer-timesheets', compact('timesheets', 'acceptedJobs', 'activeTimesheet', 'isAdmin'));
    }

    /**
     * Client/Admin: View timesheets for their jobs (or all if admin)
     */
    public function clientIndex()
    {
        $user = auth()->user();
        $isAdmin = $user->role_id == 1 || ($user->role->slug ?? '') === 'admin';
        $hasDateColumn = Schema::hasColumn('timesheets', 'date');
        $orderColumn = $hasDateColumn ? 'date' : 'created_at';
        
        if ($isAdmin) {
            // Admin can see all timesheets
            $timesheets = Timesheet::with(['contract', 'carer', 'client'])
                ->orderBy($orderColumn, 'desc')
                ->paginate(20);
            
            // All pending timesheets
            $pendingTimesheets = Timesheet::where('status', 'pending')
                ->whereNotNull('clock_out')
                ->with(['contract', 'carer', 'client'])
                ->get();
        } else {
            // Get all jobs posted by this client
            $jobIds = Contract::where('user_id', $user->id)->pluck('id');
            
            // Get accepted application IDs for these jobs (service providers who were selected)
            $acceptedApplicationIds = JobApplication::whereIn('contract_id', $jobIds)
                ->where('status', 'accepted')
                ->pluck('id');
            
            // Get contracts filled by these applications
            $filledContractIds = Contract::whereIn('filled_by_application_id', $acceptedApplicationIds)
                ->pluck('id');
            
            // Also get contracts where the application is accepted (even if not filled yet)
            $acceptedContractIds = JobApplication::whereIn('contract_id', $jobIds)
                ->where('status', 'accepted')
                ->pluck('contract_id')
                ->unique();
            
            // Combine all contract IDs where service providers have been accepted
            $validContractIds = $filledContractIds->merge($acceptedContractIds)->unique();
            
            // Get timesheets only for contracts where service providers have been accepted
            $timesheets = Timesheet::whereIn('contract_id', $validContractIds)
                ->whereIn('contract_id', $jobIds) // Ensure it's still the client's job
                ->with(['contract', 'carer'])
                ->orderBy($orderColumn, 'desc')
                ->paginate(20);
            
            // Separate pending and processed
            $pendingTimesheets = Timesheet::whereIn('contract_id', $validContractIds)
                ->whereIn('contract_id', $jobIds) // Ensure it's still the client's job
                ->where('status', 'pending')
                ->whereNotNull('clock_out')
                ->with(['contract', 'carer'])
                ->get();
        }
        
        return view('app.pages.client-timesheets', compact('timesheets', 'pendingTimesheets', 'isAdmin'));
    }

    /**
     * Carer: Clock in
     */
    public function clockIn(Request $request)
    {
        $user = auth()->user();
        
        // Check if already clocked in
        $activeTimesheet = Timesheet::where('carer_id', $user->id)
            ->whereNull('clock_out')
            ->first();
        
        if ($activeTimesheet) {
            return back()->with('error', 'You are already clocked in. Please clock out first.');
        }
        
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
        ]);
        
        $contract = Contract::findOrFail($request->contract_id);
        
        // Verify that this carer has been accepted for this job
        $acceptedApplication = JobApplication::where('contract_id', $contract->id)
            ->where('carer_id', $user->id)
            ->where('status', 'accepted')
            ->first();
        
        // Also check if the contract was filled by this carer's application
        $isFilledByCarer = $contract->filled_by_application_id && 
            JobApplication::where('id', $contract->filled_by_application_id)
                ->where('carer_id', $user->id)
                ->where('status', 'accepted')
                ->exists();
        
        if (!$acceptedApplication && !$isFilledByCarer) {
            return back()->with('error', 'You must be accepted for this job before you can clock in.');
        }
        
        // Get the job application ID if available
        $jobApplicationId = null;
        if ($acceptedApplication) {
            $jobApplicationId = $acceptedApplication->id;
        } elseif ($contract->filled_by_application_id) {
            $filledApp = JobApplication::find($contract->filled_by_application_id);
            if ($filledApp && $filledApp->carer_id === $user->id) {
                $jobApplicationId = $filledApp->id;
            }
        }
        
        // FACE VERIFICATION REQUIRED: Check if verification photo is provided
        if (!$request->hasFile('verification_photo') && !$request->has('verification_photo_base64')) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Face verification required. Please take a selfie to clock in.',
                    'requires_verification' => true
                ], 400);
            }
            return back()->with('error', 'Face verification required. Please take a selfie to clock in.');
        }
        
        // Handle face verification
        $verificationResult = null;
        $verificationPhotoPath = null;
        
        if ($request->hasFile('verification_photo')) {
            // Handle file upload
            $file = $request->file('verification_photo');
            $fileName = 'clock_in_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $verificationPhotoPath = $file->storeAs('public/timesheet-verification', $fileName);
            $verificationPhotoPath = 'storage/' . str_replace('public/', '', $verificationPhotoPath);
        } elseif ($request->has('verification_photo_base64')) {
            // Handle base64 image from mobile app
            $imageData = $request->verification_photo_base64;
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = base64_decode($imageData);
            
            $fileName = 'clock_in_' . $user->id . '_' . time() . '.jpg';
            $path = 'timesheet-verification/' . $fileName;
            Storage::disk('public')->put($path, $imageData);
            $verificationPhotoPath = 'storage/' . $path;
        }
        
        // Perform face verification
        $faceService = new FaceVerificationService();
        $verificationResult = $faceService->verifyFace($user, $verificationPhotoPath);
        
        // If verification fails, block clock in
        if (!$verificationResult['verified']) {
            // Delete uploaded photo if verification failed
            if ($verificationPhotoPath && file_exists(public_path($verificationPhotoPath))) {
                @unlink(public_path($verificationPhotoPath));
            }
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Face verification failed: ' . $verificationResult['message'],
                    'verification_failed' => true,
                    'confidence' => $verificationResult['confidence'] ?? 0
                ], 403);
            }
            return back()->with('error', 'Face verification failed: ' . $verificationResult['message'] . '. Please try again.');
        }
        
        // Create timesheet entry with verification
        $workDate = now()->toDateString();
        $timesheet = Timesheet::create([
            'contract_id' => $contract->id,
            'carer_id' => $user->id,
            'client_id' => $contract->user_id,
            'job_application_id' => $jobApplicationId,
            'date' => $workDate,
            'work_date' => $workDate, // Set work_date to same as date if column exists
            'clock_in' => now(),
            'clock_in_verified' => true,
            'clock_in_verification_photo' => $verificationPhotoPath,
            'clock_in_verification_confidence' => $verificationResult['confidence'] ?? 0,
            'clock_in_verified_at' => now(),
            'hourly_rate' => $contract->hourly_rate ?? 0,
            'status' => 'pending',
            'location_lat' => $request->lat ?? null,
            'location_lng' => $request->lng ?? null,
        ]);
        
        // Notify client
        if ($contract->user_id) {
            UserNotification::create([
                'user_id' => $contract->user_id,
                'type' => 'clock_in',
                'title' => 'Carer Clocked In',
                'message' => $user->firstname . ' has started work on "' . $contract->title . '"',
                'action_url' => '/client/timesheets',
                'data' => json_encode([
                    'timesheet_id' => $timesheet->id,
                    'contract_id' => $contract->id,
                ]),
            ]);
        }
        
        // Return appropriate response based on request type
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Clocked in successfully',
                'timesheet' => [
                    'id' => $timesheet->id,
                    'clock_in' => $timesheet->clock_in->format('Y-m-d H:i:s'),
                    'verified' => $timesheet->clock_in_verified,
                    'confidence' => $timesheet->clock_in_verification_confidence,
                ]
            ]);
        }
        
        return back()->with('success', 'Clocked in successfully at ' . now()->format('H:i'));
    }

    /**
     * Carer: Clock out
     */
    public function clockOut(Request $request, Timesheet $timesheet)
    {
        // Ensure user owns this timesheet
        if ($timesheet->carer_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        if ($timesheet->clock_out) {
            return back()->with('error', 'Already clocked out.');
        }
        
        // FACE VERIFICATION REQUIRED: Check if verification photo is provided
        if (!$request->hasFile('verification_photo') && !$request->has('verification_photo_base64')) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Face verification required. Please take a selfie to clock out.',
                    'requires_verification' => true
                ], 400);
            }
            return back()->with('error', 'Face verification required. Please take a selfie to clock out.');
        }
        
        // Handle face verification
        $verificationResult = null;
        $verificationPhotoPath = null;
        
        if ($request->hasFile('verification_photo')) {
            // Handle file upload
            $file = $request->file('verification_photo');
            $fileName = 'clock_out_' . $timesheet->carer_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $verificationPhotoPath = $file->storeAs('public/timesheet-verification', $fileName);
            $verificationPhotoPath = 'storage/' . str_replace('public/', '', $verificationPhotoPath);
        } elseif ($request->has('verification_photo_base64')) {
            // Handle base64 image from mobile app
            $imageData = $request->verification_photo_base64;
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            $imageData = base64_decode($imageData);
            
            $fileName = 'clock_out_' . $timesheet->carer_id . '_' . time() . '.jpg';
            $path = 'timesheet-verification/' . $fileName;
            Storage::disk('public')->put($path, $imageData);
            $verificationPhotoPath = 'storage/' . $path;
        }
        
        // Perform face verification
        $faceService = new FaceVerificationService();
        $verificationResult = $faceService->verifyFace($timesheet->carer, $verificationPhotoPath);
        
        // If verification fails, block clock out
        if (!$verificationResult['verified']) {
            // Delete uploaded photo if verification failed
            if ($verificationPhotoPath && file_exists(public_path($verificationPhotoPath))) {
                @unlink(public_path($verificationPhotoPath));
            }
            
            return back()->with('error', 'Face verification failed: ' . $verificationResult['message'] . '. Please try again.');
        }
        
        $clockOut = now();
        $hoursWorked = $clockOut->diffInMinutes($timesheet->clock_in) / 60;
        $totalAmount = $hoursWorked * ($timesheet->hourly_rate ?? 0);
        
        $timesheet->update([
            'clock_out' => $clockOut,
            'clock_out_verified' => true,
            'clock_out_verification_photo' => $verificationPhotoPath,
            'clock_out_verification_confidence' => $verificationResult['confidence'] ?? 0,
            'clock_out_verified_at' => now(),
            'hours_worked' => round($hoursWorked, 2),
            'total_amount' => round($totalAmount, 2),
            'notes' => $request->notes ?? $timesheet->notes,
        ]);
        
        // Notify client
        if ($timesheet->client_id) {
            UserNotification::create([
                'user_id' => $timesheet->client_id,
                'type' => 'clock_out',
                'title' => 'Carer Clocked Out',
                'message' => auth()->user()->firstname . ' has finished work. Hours: ' . round($hoursWorked, 2) . '. Please review the timesheet.',
                'action_url' => '/client/timesheets',
                'data' => json_encode([
                    'timesheet_id' => $timesheet->id,
                    'hours' => round($hoursWorked, 2),
                    'amount' => round($totalAmount, 2),
                ]),
            ]);
        }
        
        // Return appropriate response based on request type
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Clocked out successfully',
                'timesheet' => [
                    'id' => $timesheet->id,
                    'clock_out' => $timesheet->clock_out->format('Y-m-d H:i:s'),
                    'hours_worked' => round($hoursWorked, 2),
                    'total_amount' => round($totalAmount, 2),
                    'verified' => $timesheet->clock_out_verified,
                    'confidence' => $timesheet->clock_out_verification_confidence,
                ]
            ]);
        }
        
        return back()->with('success', 'Clocked out successfully. Hours worked: ' . round($hoursWorked, 2));
    }

    /**
     * Carer: Add notes to timesheet
     */
    public function addNote(Request $request, Timesheet $timesheet)
    {
        // Ensure user owns this timesheet
        if ($timesheet->carer_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $timesheet->update([
            'notes' => $request->notes,
        ]);
        
        return back()->with('success', 'Notes updated.');
    }

    /**
     * Client: Approve timesheet
     */
    public function approve(Request $request, Timesheet $timesheet)
    {
        // Ensure user is the client for this timesheet
        if ($timesheet->client_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $timesheet->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        
        // Notify carer
        UserNotification::create([
            'user_id' => $timesheet->carer_id,
            'type' => 'timesheet_approved',
            'title' => 'Timesheet Approved ✓',
            'message' => 'Your timesheet for ' . $timesheet->date . ' has been approved. Hours: ' . $timesheet->hours_worked . ', Amount: £' . number_format($timesheet->total_amount, 2),
            'action_url' => '/carer/timesheets',
            'data' => json_encode([
                'timesheet_id' => $timesheet->id,
                'amount' => $timesheet->total_amount,
            ]),
        ]);
        
        return back()->with('success', 'Timesheet approved.');
    }

    /**
     * Client: Dispute timesheet
     */
    public function dispute(Request $request, Timesheet $timesheet)
    {
        // Ensure user is the client for this timesheet
        if ($timesheet->client_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);
        
        $timesheet->update([
            'status' => 'disputed',
            'dispute_reason' => $request->reason,
        ]);
        
        // Notify carer
        UserNotification::create([
            'user_id' => $timesheet->carer_id,
            'type' => 'timesheet_disputed',
            'title' => 'Timesheet Disputed',
            'message' => 'Your timesheet for ' . $timesheet->date . ' has been disputed. Reason: ' . $request->reason,
            'action_url' => '/carer/timesheets',
            'data' => json_encode([
                'timesheet_id' => $timesheet->id,
                'reason' => $request->reason,
            ]),
        ]);
        
        return back()->with('success', 'Timesheet disputed. The carer has been notified.');
    }

    /**
     * Client/Admin: Cancel an approved timesheet
     */
    public function cancel(Request $request, Timesheet $timesheet)
    {
        $user = auth()->user();
        $userRole = $user->role->slug ?? '';
        $isAdmin = $userRole === 'admin';
        $isClient = $userRole === 'client';
        
        // Only clients (for their timesheets) and admins can cancel
        if (!$isAdmin && ($timesheet->client_id !== auth()->id() || !$isClient)) {
            abort(403, 'Unauthorized');
        }
        
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
        UserNotification::create([
            'user_id' => $timesheet->carer_id,
            'type' => 'timesheet_cancelled',
            'title' => 'Timesheet Cancelled',
            'message' => 'Your timesheet for ' . ($timesheet->date ? $timesheet->date->format('Y-m-d') : 'N/A') . ' has been cancelled. Reason: ' . $request->reason,
            'action_url' => '/carer/timesheets',
            'data' => json_encode([
                'timesheet_id' => $timesheet->id,
                'reason' => $request->reason,
            ]),
        ]);
        
        // Also notify client if admin cancelled
        if ($isAdmin && $timesheet->client_id !== auth()->id()) {
            UserNotification::create([
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
        
        return back()->with('success', 'Timesheet cancelled successfully. The carer has been notified.');
    }

    /**
     * Export timesheets to CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role_id == 1 || ($user->role->slug ?? '') === 'admin';
        $userRole = $user->role->slug ?? '';
        $isCarer = in_array($userRole, ['carer', 'childminder', 'housekeeper']);
        $isClient = $userRole === 'client';
        
        // Build query based on user role
        $query = Timesheet::with(['contract', 'carer', 'client']);
        
        if (!$isAdmin) {
            if ($isCarer) {
                // Carers see only their timesheets
                $query->where('carer_id', $user->id);
            } elseif ($isClient) {
                // Clients see timesheets for their jobs, but only from service providers who were accepted
                $jobIds = Contract::where('user_id', $user->id)->pluck('id');
                
                // Get accepted application IDs for these jobs
                $acceptedApplicationIds = JobApplication::whereIn('contract_id', $jobIds)
                    ->where('status', 'accepted')
                    ->pluck('id');
                
                // Get contracts filled by these applications
                $filledContractIds = Contract::whereIn('filled_by_application_id', $acceptedApplicationIds)
                    ->pluck('id');
                
                // Also get contracts where the application is accepted
                $acceptedContractIds = JobApplication::whereIn('contract_id', $jobIds)
                    ->where('status', 'accepted')
                    ->pluck('contract_id')
                    ->unique();
                
                // Combine all contract IDs where service providers have been accepted
                $validContractIds = $filledContractIds->merge($acceptedContractIds)->unique();
                
                // Filter timesheets by valid contracts and ensure they're the client's jobs
                $query->whereIn('contract_id', $validContractIds)
                    ->whereIn('contract_id', $jobIds);
            } else {
                // Other roles see nothing
                abort(403, 'Unauthorized');
            }
        }
        
        // Apply filters if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        // Validate date range
        if ($dateFrom && $dateTo && $dateFrom > $dateTo) {
            return back()->with('error', 'From date cannot be after To date.');
        }
        
        // Use date column if exists, otherwise use created_at
        $hasDateColumn = Schema::hasColumn('timesheets', 'date');
        $dateColumn = $hasDateColumn ? 'date' : 'created_at';
        $orderColumn = $hasDateColumn ? 'date' : 'created_at';
        
        if ($dateFrom) {
            $query->where($dateColumn, '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where($dateColumn, '<=', $dateTo);
        }
        
        $timesheets = $query->orderBy($orderColumn, 'desc')->orderBy('clock_in', 'desc')->get();
        
        // Generate CSV filename with date range if filtered
        $filename = 'timesheets';
        if ($dateFrom) {
            $filename .= '_from_' . $dateFrom;
        }
        if ($dateTo) {
            $filename .= '_to_' . $dateTo;
        }
        $filename .= '_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($timesheets, $isAdmin) {
            $file = fopen('php://output', 'w');
            
            // BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            $headers = [
                'ID',
                'Date',
                'Job Title',
                'Carer Name',
                'Client Name',
                'Clock In',
                'Clock Out',
                'Hours Worked',
                'Hourly Rate (£)',
                'Total Amount (£)',
                'Status',
                'Approved At',
                'Dispute Reason',
                'Notes',
                'Location (Lat)',
                'Location (Lng)',
                'Created At',
            ];
            
            if ($isAdmin) {
                $headers[] = 'Carer Email';
                $headers[] = 'Client Email';
            }
            
            fputcsv($file, $headers);
            
            // Data rows
            foreach ($timesheets as $timesheet) {
                $row = [
                    $timesheet->id,
                    $timesheet->date ? $timesheet->date->format('Y-m-d') : '',
                    $timesheet->contract->title ?? 'N/A',
                    $timesheet->carer ? ($timesheet->carer->firstname . ' ' . ($timesheet->carer->lastname ?? '')) : 'N/A',
                    $timesheet->client ? ($timesheet->client->firstname . ' ' . ($timesheet->client->lastname ?? '')) : 'N/A',
                    $timesheet->clock_in ? $timesheet->clock_in->format('Y-m-d H:i:s') : '',
                    $timesheet->clock_out ? $timesheet->clock_out->format('Y-m-d H:i:s') : '',
                    $timesheet->hours_worked ?? '0.00',
                    number_format($timesheet->hourly_rate ?? 0, 2),
                    number_format($timesheet->total_amount ?? 0, 2),
                    ucfirst($timesheet->status ?? 'pending'),
                    $timesheet->approved_at ? $timesheet->approved_at->format('Y-m-d H:i:s') : '',
                    $timesheet->dispute_reason ?? '',
                    $timesheet->notes ?? '',
                    $timesheet->location_lat ?? '',
                    $timesheet->location_lng ?? '',
                    $timesheet->created_at ? $timesheet->created_at->format('Y-m-d H:i:s') : '',
                ];
                
                if ($isAdmin) {
                    $row[] = $timesheet->carer->email ?? '';
                    $row[] = $timesheet->client->email ?? '';
                }
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}

