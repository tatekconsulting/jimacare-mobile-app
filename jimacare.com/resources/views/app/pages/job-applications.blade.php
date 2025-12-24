@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>📋 Job Applications</h2>
                <a href="{{ route('contract.index') }}" class="btn btn-outline-primary btn-sm">
                    Browse Jobs
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($applications->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4 class="text-muted">No applications yet</h4>
                        <p class="text-muted">When carers apply to your jobs, you'll see their applications here.</p>
                        <a href="{{ route('contract.create') }}" class="btn btn-primary">Post a Job</a>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($applications as $application)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-white">
                                    <span class="badge 
                                        @if($application->status == 'pending') badge-warning
                                        @elseif($application->status == 'accepted') badge-success
                                        @elseif($application->status == 'rejected') badge-danger
                                        @else badge-secondary
                                        @endif float-right">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                    <small class="text-muted">Applied {{ $application->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="card-body">
                                    @if($application->carer)
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="{{ asset($application->carer->profile ?? 'img/undraw_profile.svg') }}" 
                                                 alt="{{ $application->carer->firstname }}"
                                                 class="rounded-circle mr-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 font-weight-bold">
                                                    {{ $application->carer->firstname }} {{ substr($application->carer->lastname ?? '', 0, 1) }}.
                                                </h6>
                                                <small class="text-muted">{{ $application->carer->role->title ?? 'Carer' }}</small>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <p class="mb-2">
                                        <strong>Job:</strong> 
                                        <a href="{{ route('contract.show', $application->contract_id) }}">
                                            {{ $application->contract->title ?? 'N/A' }}
                                        </a>
                                    </p>
                                    
                                    @if($application->proposed_rate)
                                        <p class="mb-2">
                                            <strong>Proposed Rate:</strong> £{{ number_format($application->proposed_rate, 2) }}/hour
                                        </p>
                                    @endif
                                    
                                    @if($application->cover_letter)
                                        <p class="mb-2 text-muted" style="font-size: 0.9em;">
                                            "{{ Str::limit($application->cover_letter, 100) }}"
                                        </p>
                                    @endif
                                    
                                    {{-- Availability Calendar --}}
                                    @if($application->carer && isset($days) && isset($time_types))
                                        <hr>
                                        @include('app.partials.availability-calendar', [
                                            'user' => $application->carer,
                                            'days' => $days,
                                            'time_types' => $time_types
                                        ])
                                    @endif
                                </div>
                                <div class="card-footer bg-white">
                                    @php
                                        $contract = $application->contract;
                                        $isFilled = $contract->filled_at !== null;
                                    @endphp
                                    @if($application->status == 'pending' && !$isFilled)
                                        <form action="{{ route('job-applications.accept', $application->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to select this candidate? Other applicants will be automatically notified that another candidate was selected.');">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-block btn-sm">
                                                <i class="fa fa-check-circle mr-2"></i> Select This Candidate
                                            </button>
                                        </form>
                                        <form action="{{ route('job-applications.reject', $application->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-block btn-sm">
                                                <i class="fa fa-times mr-2"></i> Reject
                                            </button>
                                        </form>
                                    @elseif($application->status == 'accepted' && $application->carer)
                                        <div class="alert alert-success mb-2 py-2">
                                            <small><i class="fa fa-check-circle"></i> Selected Candidate</small>
                                        </div>
                                        <a href="{{ route('inbox.show', $application->carer->id) }}" class="btn btn-primary btn-block btn-sm">
                                            <i class="fa fa-comments mr-2"></i> Message Carer
                                        </a>
                                    @elseif($isFilled && $application->status != 'accepted')
                                        <div class="alert alert-info mb-0 py-2">
                                            <small><i class="fa fa-info-circle"></i> Another candidate was selected for this job.</small>
                                        </div>
                                    @else
                                        <button class="btn btn-secondary btn-block btn-sm" disabled>
                                            {{ ucfirst($application->status) }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

