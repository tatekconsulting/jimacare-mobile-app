@extends('admin.template.layout')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fa fa-file-text"></i> Job Applications Management
    </h1>
    <div>
        <a href="{{ route('dashboard.job-applications.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fa fa-sync fa-sm text-white-50"></i> Refresh
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalApplications) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-file-text fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingApplications) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-hourglass-half fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Accepted</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($acceptedApplications) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejected</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rejectedApplications) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Applications Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fa fa-list"></i> All Job Applications
        </h6>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('dashboard.job-applications.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Carer</label>
                    <select name="carer_id" class="form-control form-control-sm">
                        <option value="">All Carers</option>
                        @foreach($carers as $carer)
                            <option value="{{ $carer->id }}" {{ request('carer_id') == $carer->id ? 'selected' : '' }}>
                                {{ $carer->firstname }} {{ $carer->lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Job</label>
                    <select name="contract_id" class="form-control form-control-sm">
                        <option value="">All Jobs</option>
                        @foreach($contracts as $contract)
                            <option value="{{ $contract->id }}" {{ request('contract_id') == $contract->id ? 'selected' : '' }}>
                                {{ Str::limit($contract->title, 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, Email, Job..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('dashboard.job-applications.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>

        <!-- Applications Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Carer</th>
                        <th>Job</th>
                        <th>Proposed Rate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td>{{ $application->id }}</td>
                            <td>{{ $application->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($application->carer)
                                    <a href="{{ route('dashboard.user.show', $application->carer_id) }}" class="text-primary">
                                        {{ $application->carer->firstname }} {{ $application->carer->lastname }}
                                    </a>
                                    <br><small class="text-muted">{{ $application->carer->email ?? '' }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($application->contract)
                                    <a href="{{ route('dashboard.contract.show', $application->contract_id) }}" class="text-primary">
                                        {{ Str::limit($application->contract->title, 40) }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
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
                                <span class="badge 
                                    @if($application->status == 'pending') badge-warning
                                    @elseif($application->status == 'accepted') badge-success
                                    @elseif($application->status == 'rejected') badge-danger
                                    @else badge-secondary
                                    @endif">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('dashboard.job-applications.show', $application->id) }}" class="btn btn-info btn-sm" title="View Details">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No applications found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $applications->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

