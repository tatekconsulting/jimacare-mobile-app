@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>⏱️ Review Timesheets</h2>
                    <small class="text-muted">Download timesheets as CSV for verification and transparency</small>
                </div>
            </div>

            <!-- Export Filter Form -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa fa-filter"></i> Filter & Export Timesheets</h5>
                </div>
                <div class="card-body">
                    <form id="exportForm" method="GET" action="{{ route('timesheets.export') }}" class="row align-items-end">
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <label for="date_from"><strong>From Date</strong></label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <label for="date_to"><strong>To Date</strong></label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-download"></i> Export to CSV
                            </button>
                        </div>
                    </form>
                    <small class="text-muted mt-2 d-block">
                        <i class="fa fa-info-circle"></i> Leave dates empty to export all timesheets. Select date range to filter.
                    </small>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Pending Approval Section -->
            @if($pendingTimesheets->isNotEmpty())
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <strong>⚠️ Pending Approval ({{ $pendingTimesheets->count() }})</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Carer</th>
                                        <th>Job</th>
                                        <th>Date</th>
                                        <th>Hours</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingTimesheets as $timesheet)
                                        <tr>
                                            <td>
                                                @if($timesheet->carer)
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ asset($timesheet->carer->profile ?? 'img/undraw_profile.svg') }}" 
                                                             class="rounded-circle mr-2" 
                                                             style="width: 35px; height: 35px; object-fit: cover;">
                                                        <div>
                                                            <strong>{{ $timesheet->carer->firstname }} {{ $timesheet->carer->lastname ?? '' }}</strong>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $timesheet->contract->title ?? 'N/A' }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($timesheet->date)->format('d M Y') }}
                                                <br>
                                                <small class="text-muted">
                                                    {{ $timesheet->clock_in->format('H:i') }} - {{ $timesheet->clock_out->format('H:i') }}
                                                </small>
                                            </td>
                                            <td><strong>{{ number_format($timesheet->hours_worked, 2) }}h</strong></td>
                                            <td><strong class="text-success">£{{ number_format($timesheet->total_amount, 2) }}</strong></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <form action="{{ route('timesheet.approve', $timesheet->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            ✓ Approve
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            data-toggle="modal" 
                                                            data-target="#disputeModal{{ $timesheet->id }}">
                                                        ✗ Dispute
                                                    </button>
                                                </div>
                                                
                                                <!-- Dispute Modal -->
                                                <div class="modal fade" id="disputeModal{{ $timesheet->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('timesheet.dispute', $timesheet->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Dispute Timesheet</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Reason for dispute:</label>
                                                                        <textarea name="reason" class="form-control" rows="3" required 
                                                                                  placeholder="Please explain why you're disputing this timesheet..."></textarea>
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
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- All Timesheets History -->
            <h4 class="mb-3">All Timesheets</h4>
            
            @if($timesheets->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h5 class="text-muted">No timesheets yet</h5>
                        <p class="text-muted">Timesheets from your carers will appear here.</p>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Carer</th>
                                    <th>Job</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Hours</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timesheets as $timesheet)
                                    <tr>
                                        <td>
                                            @if($timesheet->carer)
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset($timesheet->carer->profile ?? 'img/undraw_profile.svg') }}" 
                                                         class="rounded-circle mr-2" 
                                                         style="width: 35px; height: 35px; object-fit: cover;">
                                                    <span>{{ $timesheet->carer->firstname }} {{ $timesheet->carer->lastname ?? '' }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $timesheet->contract->title ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($timesheet->date)->format('d M Y') }}</td>
                                        <td>
                                            @if($timesheet->clock_in && $timesheet->clock_out)
                                                {{ $timesheet->clock_in->format('H:i') }} - {{ $timesheet->clock_out->format('H:i') }}
                                            @elseif($timesheet->clock_in)
                                                {{ $timesheet->clock_in->format('H:i') }} - <span class="text-warning">In progress</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($timesheet->hours_worked)
                                                {{ number_format($timesheet->hours_worked, 2) }}h
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($timesheet->total_amount)
                                                £{{ number_format($timesheet->total_amount, 2) }}
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
                                                {{ ucfirst($timesheet->status) }}
                                            </span>
                                            @if($timesheet->approved_at && $timesheet->status != 'cancelled')
                                                <br>
                                                <small class="text-success">{{ $timesheet->approved_at->format('d M') }}</small>
                                            @endif
                                            @if($timesheet->status == 'cancelled' && $timesheet->cancelled_at)
                                                <br>
                                                <small class="text-muted">Cancelled: {{ $timesheet->cancelled_at->format('d M Y') }}</small>
                                                @if($timesheet->cancellation_reason)
                                                    <br>
                                                    <small class="text-muted" title="{{ $timesheet->cancellation_reason }}">
                                                        <i class="fa fa-info-circle"></i> {{ Str::limit($timesheet->cancellation_reason, 30) }}
                                                    </small>
                                                @endif
                                            @endif
                                            @if($timesheet->status == 'approved')
                                                <br>
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-1" 
                                                        data-toggle="modal" 
                                                        data-target="#cancelModal{{ $timesheet->id }}">
                                                    <i class="fa fa-times"></i> Cancel
                                                </button>
                                                
                                                <!-- Cancel Modal -->
                                                <div class="modal fade" id="cancelModal{{ $timesheet->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('timesheet.cancel', $timesheet->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Cancel Timesheet</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">
                                                                        <span>&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="text-warning">
                                                                        <i class="fa fa-exclamation-triangle"></i> 
                                                                        Are you sure you want to cancel this approved timesheet? The carer will be notified.
                                                                    </p>
                                                                    <div class="form-group">
                                                                        <label>Reason for cancellation: <span class="text-danger">*</span></label>
                                                                        <textarea name="reason" class="form-control" rows="3" required 
                                                                                  placeholder="Please explain why you're cancelling this timesheet..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Cancel Timesheet</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $timesheets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

