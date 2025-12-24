@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>📋 My Applications</h2>
                <a href="{{ route('contract.index') }}" class="btn btn-primary btn-sm">
                    Find More Jobs
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3>{{ $applications->where('status', 'pending')->count() }}</h3>
                            <small>Pending</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ $applications->where('status', 'accepted')->count() }}</h3>
                            <small>Accepted</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3>{{ $applications->where('status', 'rejected')->count() }}</h3>
                            <small>Rejected</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h3>{{ $applications->count() }}</h3>
                            <small>Total</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($applications->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h4 class="text-muted">No applications yet</h4>
                        <p class="text-muted">Start applying to jobs to see your applications here.</p>
                        <a href="{{ route('contract.index') }}" class="btn btn-primary">Browse Jobs</a>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Job</th>
                                    <th>Rate</th>
                                    <th>Applied</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $application)
                                    <tr>
                                        <td>
                                            @if($application->contract)
                                                <a href="{{ route('contract.show', $application->contract_id) }}" class="font-weight-bold text-dark">
                                                    {{ $application->contract->title ?? 'Job #' . $application->contract_id }}
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $application->contract->address ?? '' }}
                                                </small>
                                            @else
                                                <span class="text-muted">Job no longer available</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($application->proposed_rate)
                                                £{{ number_format($application->proposed_rate, 2) }}/hr
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $application->created_at->format('d M Y') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($application->status == 'pending') badge-warning
                                                @elseif($application->status == 'accepted') badge-success
                                                @elseif($application->status == 'rejected') badge-danger
                                                @elseif($application->status == 'withdrawn') badge-secondary
                                                @else badge-light
                                                @endif">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                            @if($application->responded_at)
                                                <br>
                                                <small class="text-muted">{{ $application->responded_at->diffForHumans() }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($application->status == 'pending')
                                                <form action="{{ route('job-applications.withdraw', $application->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Withdraw this application?')">
                                                        Withdraw
                                                    </button>
                                                </form>
                                            @elseif($application->status == 'accepted' && $application->contract && $application->contract->user)
                                                <a href="{{ route('inbox.show', $application->contract->user_id) }}" class="btn btn-primary btn-sm">
                                                    Message Client
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

