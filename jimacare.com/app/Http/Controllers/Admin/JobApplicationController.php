<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\Contract;
use App\Models\User;

class JobApplicationController extends Controller
{
    /**
     * Display all job applications with filters
     */
    public function index(Request $request)
    {
        $query = JobApplication::with(['contract', 'carer'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('carer_id') && $request->carer_id) {
            $query->where('carer_id', $request->carer_id);
        }
        
        if ($request->has('contract_id') && $request->contract_id) {
            $query->where('contract_id', $request->contract_id);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('carer', function($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('contract', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            });
        }
        
        $applications = $query->paginate(25);
        
        // Statistics
        $totalApplications = JobApplication::count();
        $pendingApplications = JobApplication::where('status', 'pending')->count();
        $acceptedApplications = JobApplication::where('status', 'accepted')->count();
        $rejectedApplications = JobApplication::where('status', 'rejected')->count();
        
        // Get carers and contracts for filter dropdowns
        $carers = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['carer', 'childminder', 'housekeeper']);
        })->orderBy('firstname')->get();
        
        $contracts = Contract::where('status', 'active')->orderBy('title')->get();
        
        return view('admin.pages.job-applications.index', compact(
            'applications',
            'totalApplications',
            'pendingApplications',
            'acceptedApplications',
            'rejectedApplications',
            'carers',
            'contracts'
        ));
    }
    
    /**
     * View single application details
     */
    public function show(JobApplication $application)
    {
        $application->load(['contract', 'carer']);
        return view('admin.pages.job-applications.show', compact('application'));
    }
}

