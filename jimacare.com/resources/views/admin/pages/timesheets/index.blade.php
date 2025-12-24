@extends('admin.template.layout')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fa fa-clock-o"></i> Timesheets Management
    </h1>
    <div>
        <a href="{{ route('timesheets.export', request()->all()) }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm mr-2">
            <i class="fa fa-download fa-sm text-white-50"></i> Export CSV
        </a>
        <a href="{{ route('dashboard.timesheets.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalTimesheets) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-clock-o fa-2x text-gray-300"></i>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingTimesheets) }}</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($approvedTimesheets) }}</div>
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
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Disputed</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($disputedTimesheets) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Cancelled</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($cancelledTimesheets ?? 0) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-ban fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Amount</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($totalAmount, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fa fa-pound fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timesheets Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fa fa-list"></i> All Timesheets
        </h6>
    </div>
    <div class="card-body">
        <!-- Advanced Filter Form -->
        <form method="GET" action="{{ route('dashboard.timesheets.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="disputed" {{ request('status') == 'disputed' ? 'selected' : '' }}>Disputed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Service Type</label>
                    <select name="carer_type" class="form-control form-control-sm">
                        <option value="">All Types</option>
                        <option value="carer" {{ request('carer_type') == 'carer' ? 'selected' : '' }}>👴 Carers</option>
                        <option value="childminder" {{ request('carer_type') == 'childminder' ? 'selected' : '' }}>👶 Childminders</option>
                        <option value="housekeeper" {{ request('carer_type') == 'housekeeper' ? 'selected' : '' }}>🏠 Housekeepers</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label>To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label>Client</label>
                    <select name="client_id" class="form-control form-control-sm">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->firstname }} {{ $client->lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, Email, Job..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('dashboard.timesheets.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>

        <!-- Timesheets Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Carer</th>
                        <th>Client Name</th>
                        <th>Job</th>
                        <th>Time</th>
                        <th>Hours</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($timesheets as $timesheet)
                        <tr>
                            <td>{{ $timesheet->id }}</td>
                            <td>{{ $timesheet->date ? $timesheet->date->format('d M Y') : 'N/A' }}</td>
                            <td>
                                @if($timesheet->carer)
                                    <a href="{{ route('dashboard.user.show', $timesheet->carer_id) }}" class="text-primary">
                                        <strong>{{ $timesheet->carer->firstname }} {{ $timesheet->carer->lastname ?? '' }}</strong>
                                    </a>
                                    <br><small class="text-muted">{{ $timesheet->carer->email ?? '' }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($timesheet->client)
                                    <a href="{{ route('dashboard.user.show', $timesheet->client_id) }}" class="text-primary">
                                        <strong>{{ $timesheet->client->firstname }} {{ $timesheet->client->lastname ?? '' }}</strong>
                                    </a>
                                    <br><small class="text-muted">{{ $timesheet->client->email ?? '' }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($timesheet->contract)
                                    <a href="{{ route('dashboard.contract.show', $timesheet->contract_id) }}" class="text-primary">
                                        {{ Str::limit($timesheet->contract->title, 30) }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($timesheet->clock_in && $timesheet->clock_out)
                                    <small>{{ $timesheet->clock_in->format('H:i') }} - {{ $timesheet->clock_out->format('H:i') }}</small>
                                @elseif($timesheet->clock_in)
                                    <span class="badge badge-warning">{{ $timesheet->clock_in->format('H:i') }} (Active)</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $timesheet->hours_worked ? number_format($timesheet->hours_worked, 2) . 'h' : '-' }}</td>
                            <td>£{{ number_format($timesheet->hourly_rate ?? 0, 2) }}/hr</td>
                            <td>
                                @if($timesheet->total_amount)
                                    <strong class="text-success">£{{ number_format($timesheet->total_amount, 2) }}</strong>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if($timesheet->status == 'pending') badge-warning
                                    @elseif($timesheet->status == 'approved') badge-success
                                    @elseif($timesheet->status == 'disputed') badge-danger
                                    @elseif($timesheet->status == 'cancelled') badge-dark
                                    @else badge-secondary
                                    @endif">
                                    {{ ucfirst($timesheet->status ?? 'pending') }}
                                </span>
                                @if($timesheet->status == 'cancelled' && $timesheet->cancelled_at)
                                    <br>
                                    <small class="text-muted">{{ $timesheet->cancelled_at->format('d M Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('dashboard.timesheets.show', $timesheet->id) }}" class="btn btn-info btn-sm" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if($timesheet->status == 'pending' && $timesheet->clock_out)
                                        <form action="{{ route('dashboard.timesheets.approve', $timesheet->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Approve" onclick="return confirm('Approve this timesheet?')">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#disputeModal{{ $timesheet->id }}" title="Dispute">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    @endif
                                    @if($timesheet->status == 'approved')
                                        <button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#cancelModal{{ $timesheet->id }}" title="Cancel">
                                            <i class="fa fa-ban"></i>
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Dispute Modal -->
                                <div class="modal fade" id="disputeModal{{ $timesheet->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('dashboard.timesheets.dispute', $timesheet->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Dispute Timesheet #{{ $timesheet->id }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Reason:</label>
                                                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Submit Dispute</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Cancel Modal -->
                                <div class="modal fade" id="cancelModal{{ $timesheet->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('dashboard.timesheets.cancel', $timesheet->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cancel Timesheet #{{ $timesheet->id }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-warning">
                                                        <i class="fa fa-exclamation-triangle"></i> 
                                                        Are you sure you want to cancel this approved timesheet? Both the carer and client will be notified.
                                                    </p>
                                                    <div class="form-group">
                                                        <label>Reason for cancellation: <span class="text-danger">*</span></label>
                                                        <textarea name="reason" class="form-control" rows="3" required 
                                                                  placeholder="Please explain why you're cancelling this timesheet..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-dark">Cancel Timesheet</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">No timesheets found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $timesheets->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

