<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\JobInvitation;
use App\Models\Contract;
use App\Models\UserNotification;
use App\Models\User;
use App\Models\Day;
use App\Models\TimeType;
use App\Notifications\JobApplicationReceived;
use App\Notifications\ApplicationSelected;
use App\Notifications\ApplicationNotSelected;

class JobApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Client/Admin: View all applications for their jobs (or all if admin)
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->role_id == 1 || ($user->role->slug ?? '') === 'admin';
        
        if ($isAdmin) {
            // Admin can see all applications
            $applications = JobApplication::with(['contract', 'carer', 'carer.days', 'carer.time_availables'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Get all jobs posted by this client
            $jobIds = Contract::where('user_id', $user->id)->pluck('id');
            
            // Get all applications for these jobs
            $applications = JobApplication::whereIn('contract_id', $jobIds)
                ->with(['contract', 'carer', 'carer.days', 'carer.time_availables'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }
        
        // Get days and time_types for availability calendar
        $days = Day::all();
        $time_types = TimeType::all();
        
        return view('app.pages.job-applications', compact('applications', 'isAdmin', 'days', 'time_types'));
    }

    /**
     * Client: View applications for a specific job
     */
    public function viewApplications(Contract $contract)
    {
        // Ensure user owns this job
        if ($contract->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $applications = JobApplication::where('contract_id', $contract->id)
            ->with('carer')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('app.pages.job-applications-detail', compact('contract', 'applications'));
    }

    /**
     * Carer: Apply to a job
     */
    public function apply(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user is a carer/childminder/housekeeper
        $userRole = $user->role->slug ?? '';
        if (!in_array($userRole, ['carer', 'childminder', 'housekeeper'])) {
            return back()->with('error', 'Only carers can apply to jobs.');
        }
        
        // Check if already applied
        $existingApplication = JobApplication::where('contract_id', $contract->id)
            ->where('carer_id', $user->id)
            ->first();
        
        if ($existingApplication) {
            return back()->with('error', 'You have already applied to this job.');
        }
        
        // Create application
        $application = JobApplication::create([
            'contract_id' => $contract->id,
            'carer_id' => $user->id,
            'cover_letter' => $request->cover_letter ?? null,
            'proposed_rate' => $request->proposed_rate ?? $contract->hourly_rate,
            'status' => 'pending',
        ]);
        
        // Notify the job owner (in-app notification)
        if ($contract->user_id) {
            $client = User::find($contract->user_id);
            if ($client) {
                UserNotification::create([
                    'user_id' => $contract->user_id,
                    'type' => 'job_application',
                    'title' => 'New Job Application',
                    'message' => $user->firstname . ' ' . ($user->lastname ?? '') . ' has applied to your job: ' . $contract->title,
                    'action_url' => '/job-applications',
                    'data' => json_encode([
                        'application_id' => $application->id,
                        'carer_id' => $user->id,
                        'contract_id' => $contract->id,
                    ]),
                ]);
                
                // Send email notification to client
                try {
                    $client->notify(new JobApplicationReceived($application));
                } catch (\Exception $e) {
                    \Log::error('Failed to send job application email notification', [
                        'client_id' => $client->id,
                        'application_id' => $application->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return back()->with('success', 'Application submitted successfully!');
    }

    /**
     * Carer/Admin: View their own applications (or all if admin)
     */
    public function myApplications()
    {
        $user = auth()->user();
        $isAdmin = $user->role_id == 1 || ($user->role->slug ?? '') === 'admin';
        
        if ($isAdmin) {
            // Admin can see all applications
            $applications = JobApplication::with('contract')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Carer sees only their applications
            $applications = JobApplication::where('carer_id', $user->id)
                ->with('contract')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }
        
        return view('app.pages.my-applications', compact('applications', 'isAdmin'));
    }

    /**
     * Client: Accept/Select an application (marks job as filled)
     */
    public function accept(Request $request, JobApplication $application)
    {
        $contract = $application->contract;
        
        // Ensure user owns this job
        if ($contract->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        // Check if job is already filled
        if ($contract->filled_at) {
            return back()->with('error', 'This job has already been filled.');
        }
        
        // Mark this application as accepted
        $application->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);
        
        // Mark job as filled
        $contract->update([
            'filled_at' => now(),
            'filled_by_application_id' => $application->id,
        ]);
        
        // Reject all other pending applications for this job
        $otherApplications = JobApplication::where('contract_id', $contract->id)
            ->where('id', '!=', $application->id)
            ->where('status', 'pending')
            ->get();
        
        foreach ($otherApplications as $otherApp) {
            $otherApp->update([
                'status' => 'rejected',
                'responded_at' => now(),
                'rejection_reason' => 'Another candidate was selected for this position.',
            ]);
            
            // Notify rejected applicants
            if ($otherApp->carer) {
                UserNotification::create([
                    'user_id' => $otherApp->carer_id,
                    'type' => 'application_rejected',
                    'title' => 'Application Update',
                    'message' => 'Another candidate was selected for "' . $contract->title . '".',
                    'action_url' => '/my-applications',
                    'data' => json_encode([
                        'application_id' => $otherApp->id,
                        'contract_id' => $contract->id,
                    ]),
                ]);
                
                // Send email notification
                try {
                    $otherApp->carer->notify(new ApplicationNotSelected($otherApp));
                } catch (\Exception $e) {
                    \Log::error('Failed to send rejection email', [
                        'carer_id' => $otherApp->carer_id,
                        'application_id' => $otherApp->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        // Notify selected carer (in-app)
        UserNotification::create([
            'user_id' => $application->carer_id,
            'type' => 'application_accepted',
            'title' => 'Application Accepted! 🎉',
            'message' => 'Your application for "' . $contract->title . '" has been accepted!',
            'action_url' => '/inbox/' . $contract->user_id,
            'data' => json_encode([
                'application_id' => $application->id,
                'contract_id' => $contract->id,
            ]),
        ]);
        
        // Send email notification to selected carer
        if ($application->carer) {
            try {
                $application->carer->notify(new ApplicationSelected($application));
            } catch (\Exception $e) {
                \Log::error('Failed to send selection email', [
                    'carer_id' => $application->carer_id,
                    'application_id' => $application->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return back()->with('success', 'Application accepted! The job is now filled. Other applicants have been notified.');
    }

    /**
     * Client: Reject an application
     */
    public function reject(Request $request, JobApplication $application)
    {
        $contract = $application->contract;
        
        // Ensure user owns this job
        if ($contract->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $application->update([
            'status' => 'rejected',
            'responded_at' => now(),
            'rejection_reason' => $request->reason ?? null,
        ]);
        
        // Notify the carer
        UserNotification::create([
            'user_id' => $application->carer_id,
            'type' => 'application_rejected',
            'title' => 'Application Update',
            'message' => 'Your application for "' . $contract->title . '" was not selected this time.',
            'action_url' => '/my-applications',
            'data' => json_encode([
                'application_id' => $application->id,
                'contract_id' => $contract->id,
            ]),
        ]);
        
        return back()->with('success', 'Application rejected.');
    }

    /**
     * Carer: Withdraw an application
     */
    public function withdraw(JobApplication $application)
    {
        // Ensure user owns this application
        if ($application->carer_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        if ($application->status !== 'pending') {
            return back()->with('error', 'You can only withdraw pending applications.');
        }
        
        $application->update(['status' => 'withdrawn']);
        
        return back()->with('success', 'Application withdrawn.');
    }

    /**
     * Handle GET requests to job accept route (redirects to job page with error)
     */
    public function acceptJobGet(Contract $contract)
    {
        return redirect()->route('contract.show', $contract->id)
            ->with('error', 'Please use the "Accept Job" button on the job page to accept this job.');
    }

    /**
     * Carer: Accept a job posting (creates application automatically)
     */
    public function acceptJob(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user is a carer/childminder/housekeeper
        $userRole = $user->role->slug ?? '';
        if (!in_array($userRole, ['carer', 'childminder', 'housekeeper'])) {
            return back()->with('error', 'Only service providers can accept jobs.');
        }
        
        // Check if already applied or accepted
        $existingApplication = JobApplication::where('contract_id', $contract->id)
            ->where('carer_id', $user->id)
            ->first();
        
        if ($existingApplication) {
            if ($existingApplication->status === 'accepted') {
                return back()->with('info', 'You have already accepted this job.');
            }
            // Update existing application to accepted
            $existingApplication->update([
                'status' => 'accepted',
                'response_type' => 'applied',
            ]);
        } else {
            // Create new application with accepted status
            $application = JobApplication::create([
                'contract_id' => $contract->id,
                'carer_id' => $user->id,
                'cover_letter' => $request->cover_letter ?? null,
                'proposed_rate' => $request->proposed_rate ?? $contract->hourly_rate,
                'status' => 'accepted',
                'response_type' => 'applied',
            ]);
        }
        
        // Notify the job owner
        if ($contract->user_id) {
            UserNotification::create([
                'user_id' => $contract->user_id,
                'type' => 'job_accepted',
                'title' => 'Job Accepted',
                'message' => $user->firstname . ' ' . ($user->lastname ?? '') . ' has accepted your job: ' . $contract->title,
                'action_url' => '/job-applications',
                'data' => json_encode([
                    'contract_id' => $contract->id,
                    'carer_id' => $user->id,
                ]),
            ]);
        }
        
        return back()->with('success', 'Job accepted! The client has been notified.');
    }

    /**
     * Carer: Reject a job posting
     */
    public function rejectJob(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Check if user is a carer/childminder/housekeeper
        $userRole = $user->role->slug ?? '';
        if (!in_array($userRole, ['carer', 'childminder', 'housekeeper'])) {
            return back()->with('error', 'Only service providers can reject jobs.');
        }
        
        // Check if already applied
        $existingApplication = JobApplication::where('contract_id', $contract->id)
            ->where('carer_id', $user->id)
            ->first();
        
        if ($existingApplication) {
            // Update to rejected
            $existingApplication->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason ?? 'Not interested',
            ]);
        } else {
            // Create rejected application (for tracking)
            JobApplication::create([
                'contract_id' => $contract->id,
                'carer_id' => $user->id,
                'status' => 'rejected',
                'response_type' => 'applied',
                'rejection_reason' => $request->reason ?? 'Not interested',
            ]);
        }
        
        return back()->with('info', 'Job rejected. You can still apply later if you change your mind.');
    }

    /**
     * Client: Invite a carer to a job
     */
    public function invite(Request $request, Contract $contract)
    {
        $user = auth()->user();
        
        // Ensure user owns this job
        if ($contract->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'carer_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);
        
        $carer = User::findOrFail($request->carer_id);
        
        // Check if already invited
        $existingInvitation = JobInvitation::where('contract_id', $contract->id)
            ->where('carer_id', $carer->id)
            ->first();
        
        if ($existingInvitation) {
            return back()->with('error', 'This carer has already been invited to this job.');
        }
        
        // Create invitation
        $invitation = JobInvitation::create([
            'contract_id' => $contract->id,
            'client_id' => $user->id,
            'carer_id' => $carer->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);
        
        // Notify the carer
        UserNotification::create([
            'user_id' => $carer->id,
            'type' => 'job_invitation',
            'title' => 'Job Invitation',
            'message' => $user->firstname . ' ' . ($user->lastname ?? '') . ' has invited you to apply for: ' . $contract->title,
            'action_url' => '/job/' . $contract->id,
            'data' => json_encode([
                'invitation_id' => $invitation->id,
                'contract_id' => $contract->id,
                'client_id' => $user->id,
            ]),
        ]);
        
        return back()->with('success', 'Invitation sent to ' . $carer->firstname . '!');
    }

    /**
     * Carer: Accept a job invitation
     */
    public function acceptInvitation(JobInvitation $invitation)
    {
        $user = auth()->user();
        
        // Ensure user is the invited carer
        if ($invitation->carer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation has already been responded to.');
        }
        
        // Update invitation
        $invitation->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);
        
        // Create application automatically
        $application = JobApplication::create([
            'contract_id' => $invitation->contract_id,
            'carer_id' => $user->id,
            'invitation_id' => $invitation->id,
            'status' => 'accepted',
            'response_type' => 'invited',
        ]);
        
        // Notify the client
        UserNotification::create([
            'user_id' => $invitation->client_id,
            'type' => 'invitation_accepted',
            'title' => 'Invitation Accepted! 🎉',
            'message' => $user->firstname . ' ' . ($user->lastname ?? '') . ' has accepted your invitation for: ' . $invitation->contract->title,
            'action_url' => '/job-applications',
            'data' => json_encode([
                'application_id' => $application->id,
                'invitation_id' => $invitation->id,
                'contract_id' => $invitation->contract_id,
            ]),
        ]);
        
        return back()->with('success', 'Invitation accepted! You can now message the client.');
    }

    /**
     * Carer: Reject a job invitation
     */
    public function rejectInvitation(Request $request, JobInvitation $invitation)
    {
        $user = auth()->user();
        
        // Ensure user is the invited carer
        if ($invitation->carer_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
        
        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation has already been responded to.');
        }
        
        // Update invitation
        $invitation->update([
            'status' => 'rejected',
            'responded_at' => now(),
            'rejection_reason' => $request->reason ?? null,
        ]);
        
        // Notify the client
        UserNotification::create([
            'user_id' => $invitation->client_id,
            'type' => 'invitation_rejected',
            'title' => 'Invitation Declined',
            'message' => $user->firstname . ' ' . ($user->lastname ?? '') . ' has declined your invitation for: ' . $invitation->contract->title,
            'action_url' => '/job/' . $invitation->contract_id,
            'data' => json_encode([
                'invitation_id' => $invitation->id,
                'contract_id' => $invitation->contract_id,
            ]),
        ]);
        
        return back()->with('info', 'Invitation declined.');
    }
}

