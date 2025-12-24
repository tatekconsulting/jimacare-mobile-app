@extends('admin.template.layout')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fa fa-clock-o"></i> Timesheet Details #{{ $timesheet->id }}
    </h1>
    <div>
        <a href="{{ route('dashboard.timesheets.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Timesheets
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<div class="row">
    <!-- Timesheet Information -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Timesheet Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Service Provider:</strong><br>
                        @if($timesheet->carer)
                            <a href="{{ route('dashboard.user.edit', ['user' => $timesheet->carer->id]) }}">
                                {{ $timesheet->carer->firstname }} {{ $timesheet->carer->lastname ?? '' }}
                            </a>
                            <br>
                            <small class="text-muted">{{ $timesheet->carer->role->title ?? 'N/A' }} | {{ $timesheet->carer->email ?? '' }}</small>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Client:</strong><br>
                        @if($timesheet->client)
                            <a href="{{ route('dashboard.user.edit', ['user' => $timesheet->client->id]) }}">
                                {{ $timesheet->client->firstname }} {{ $timesheet->client->lastname ?? '' }}
                            </a>
                            <br>
                            <small class="text-muted">{{ $timesheet->client->email ?? '' }}</small>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Job/Contract:</strong><br>
                        @if($timesheet->contract)
                            <a href="{{ route('dashboard.contract.show', ['contract' => $timesheet->contract->id]) }}">
                                {{ $timesheet->contract->title ?? 'Job #' . $timesheet->contract->id }}
                            </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge badge-lg 
                            @if($timesheet->status == 'approved') badge-success
                            @elseif($timesheet->status == 'pending') badge-warning
                            @elseif($timesheet->status == 'disputed') badge-danger
                            @elseif($timesheet->status == 'cancelled') badge-secondary
                            @else badge-info
                            @endif">
                            {{ ucfirst($timesheet->status) }}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Date:</strong><br>
                        @if($timesheet->date)
                            {{ $timesheet->date->format('M d, Y') }}
                        @elseif($timesheet->work_date)
                            {{ \Carbon\Carbon::parse($timesheet->work_date)->format('M d, Y') }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Clock In:</strong><br>
                        @if($timesheet->clock_in)
                            {{ $timesheet->clock_in->format('M d, Y H:i:s') }}
                        @else
                            <span class="text-muted">Not clocked in</span>
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Clock Out:</strong><br>
                        @if($timesheet->clock_out)
                            {{ $timesheet->clock_out->format('M d, Y H:i:s') }}
                        @else
                            <span class="text-muted">Not clocked out</span>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <strong>Hours Worked</strong><br>
                        <span class="h4">
                            @if($timesheet->hours_worked)
                                {{ number_format($timesheet->hours_worked, 2) }}h
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <strong>Hourly Rate</strong><br>
                        <span class="h4">£{{ number_format($timesheet->hourly_rate ?? 0, 2) }}</span>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <strong>Total Amount</strong><br>
                        <span class="h4 text-success">
                            @if($timesheet->total_amount)
                                £{{ number_format($timesheet->total_amount, 2) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
                    </div>
                </div>
                @if($timesheet->notes)
                <hr>
                <div class="mb-3">
                    <strong>Notes:</strong><br>
                    <p class="text-muted">{{ $timesheet->notes }}</p>
                </div>
                @endif
                @if($timesheet->dispute_reason)
                <hr>
                <div class="mb-3">
                    <strong>Dispute Reason:</strong><br>
                    <p class="text-danger">{{ $timesheet->dispute_reason }}</p>
                </div>
                @endif
                @if($timesheet->location_lat && $timesheet->location_lng)
                <hr>
                <div class="mb-3">
                    <strong>Location:</strong><br>
                    <small class="text-muted">
                        Lat: {{ $timesheet->location_lat }}, Lng: {{ $timesheet->location_lng }}
                    </small>
                    <br>
                    <a href="https://www.google.com/maps?q={{ $timesheet->location_lat }},{{ $timesheet->location_lng }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fa fa-map-marker"></i> View on Map
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
            </div>
            <div class="card-body">
                @if($timesheet->status == 'pending')
                    <form action="{{ route('dashboard.timesheets.approve', $timesheet->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Approve this timesheet?')">
                            <i class="fa fa-check"></i> Approve Timesheet
                        </button>
                    </form>
                    
                    <button type="button" class="btn btn-danger btn-block mb-2" data-toggle="modal" data-target="#disputeModal">
                        <i class="fa fa-exclamation-triangle"></i> Dispute Timesheet
                    </button>
                @endif

                @if($timesheet->status != 'cancelled')
                    <button type="button" class="btn btn-secondary btn-block" data-toggle="modal" data-target="#cancelModal">
                        <i class="fa fa-times"></i> Cancel Timesheet
                    </button>
                @endif

                @if($timesheet->approved_at)
                    <hr>
                    <small class="text-muted">
                        <strong>Approved At:</strong><br>
                        {{ $timesheet->approved_at->format('M d, Y H:i:s') }}
                    </small>
                @endif
            </div>
        </div>

        <!-- Timestamps -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Timestamps</h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>Created:</strong> {{ $timesheet->created_at->format('M d, Y H:i:s') }}<br>
                    <strong>Updated:</strong> {{ $timesheet->updated_at->format('M d, Y H:i:s') }}<br>
                    @if($timesheet->cancelled_at)
                        <strong>Cancelled:</strong> {{ $timesheet->cancelled_at->format('M d, Y H:i:s') }}<br>
                    @endif
                    @if($timesheet->paid_at)
                        <strong>Paid:</strong> {{ $timesheet->paid_at->format('M d, Y H:i:s') }}<br>
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Dispute Modal -->
<div class="modal fade" id="disputeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('dashboard.timesheets.dispute', $timesheet->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dispute Timesheet</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reason">Reason for Dispute:</label>
                        <textarea name="reason" id="reason" class="form-control" rows="4" required placeholder="Please provide a reason for disputing this timesheet..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Dispute Timesheet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('dashboard.timesheets.cancel', $timesheet->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Timesheet</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this timesheet? This action cannot be undone.</p>
                    <div class="form-group">
                        <label for="cancellation_reason">Cancellation Reason (optional):</label>
                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="3" placeholder="Optional reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Keep It</button>
                    <button type="submit" class="btn btn-danger">Yes, Cancel Timesheet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

