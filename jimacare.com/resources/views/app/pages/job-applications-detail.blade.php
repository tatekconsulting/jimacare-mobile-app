@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/job-applications') }}">Job Applications</a></li>
                    <li class="breadcrumb-item active">{{ $contract->title }}</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Applications for "{{ $contract->title }}"</h2>
                    <p class="text-muted mb-0">
                        {{ $applications->count() }} application(s) received
                    </p>
                </div>
                <a href="{{ route('contract.show', $contract->id) }}" class="btn btn-outline-primary btn-sm">
                    View Job
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
                        <h5 class="text-muted">No applications yet</h5>
                        <p class="text-muted">Carers haven't applied to this job yet. Check back later!</p>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($applications as $application)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm {{ $application->status == 'accepted' ? 'border-success' : '' }}">
                                <div class="card-header bg-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Applied {{ $application->created_at->diffForHumans() }}</small>
                                        <span class="badge 
                                            @if($application->status == 'pending') badge-warning
                                            @elseif($application->status == 'accepted') badge-success
                                            @elseif($application->status == 'rejected') badge-danger
                                            @else badge-secondary
                                            @endif">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($application->carer)
                                        <div class="text-center mb-3">
                                            <img src="{{ asset($application->carer->profile ?? 'img/undraw_profile.svg') }}" 
                                                 alt="{{ $application->carer->firstname }}"
                                                 class="rounded-circle mb-2" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                            <h5 class="mb-0">
                                                {{ $application->carer->firstname }} {{ $application->carer->lastname ?? '' }}
                                                @if($application->carer->approved)
                                                    <span class="text-primary" title="Verified">✓</span>
                                                @endif
                                            </h5>
                                            <small class="text-muted">{{ $application->carer->role->title ?? 'Carer' }}</small>
                                        </div>

                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <small class="text-muted d-block">Experience</small>
                                                <strong>{{ $application->carer->experiences->count() ?? 0 }} skills</strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Reviews</small>
                                                <strong>{{ $application->carer->reviews->count() ?? 0 }}</strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Rating</small>
                                                <strong>
                                                    @php
                                                        $rating = $application->carer->reviews->avg('stars') ?? 0;
                                                    @endphp
                                                    {{ number_format($rating, 1) }} ⭐
                                                </strong>
                                            </div>
                                        </div>
                                    @endif

                                    @if($application->proposed_rate)
                                        <p class="mb-2">
                                            <i class="fa fa-money text-success"></i>
                                            <strong>Proposed Rate:</strong> £{{ number_format($application->proposed_rate, 2) }}/hour
                                        </p>
                                    @endif

                                    @if($application->cover_letter)
                                        <div class="bg-light p-3 rounded mb-3">
                                            <small class="text-muted d-block mb-1">Cover Letter:</small>
                                            <p class="mb-0" style="font-size: 0.95em;">{{ $application->cover_letter }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer bg-white">
                                    @if($application->status == 'pending')
                                        <div class="row">
                                            <div class="col-6">
                                                <form action="{{ route('job-applications.accept', $application->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-block">
                                                        ✓ Accept
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col-6">
                                                <form action="{{ route('job-applications.reject', $application->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-block">
                                                        ✗ Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @if($application->carer)
                                            <a href="{{ route('seller.show', $application->carer->id) }}" class="btn btn-outline-primary btn-block btn-sm mt-2">
                                                View Full Profile
                                            </a>
                                        @endif
                                    @elseif($application->status == 'accepted' && $application->carer)
                                        <a href="{{ route('inbox.show', $application->carer->id) }}" class="btn btn-primary btn-block">
                                            💬 Message {{ $application->carer->firstname }}
                                        </a>
                                    @elseif($application->status == 'rejected')
                                        <button class="btn btn-secondary btn-block" disabled>
                                            Application Rejected
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

