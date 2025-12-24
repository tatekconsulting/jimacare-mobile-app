@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>⏱️ My Timesheets</h2>
                    <small class="text-muted">Download your timesheets as CSV for verification and transparency</small>
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

            <!-- Clock In/Out Section -->
            <div class="card mb-4 shadow-sm" style="border-left: 4px solid #667eea;">
                <div class="card-body">
                    @if($activeTimesheet)
                        <!-- Currently Clocked In -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-success mb-1">🟢 Currently Working</h5>
                                <p class="mb-0">
                                    <strong>Job:</strong> {{ $activeTimesheet->contract->title ?? 'N/A' }}<br>
                                    <strong>Clocked In:</strong> {{ $activeTimesheet->clock_in->format('H:i') }} 
                                    ({{ $activeTimesheet->clock_in->diffForHumans() }})
                                </p>
                            </div>
                            <form action="{{ route('timesheet.clockOut', $activeTimesheet->id) }}" method="POST">
                                @csrf
                                <div class="form-group mb-2">
                                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Add notes (optional)">
                                </div>
                                <button type="submit" class="btn btn-danger btn-lg">
                                    🔴 Clock Out
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Clock In Form -->
                        <h5 class="mb-3">Clock In to Start Work</h5>
                        @if($acceptedJobs->isEmpty())
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle"></i> You need an accepted job application before you can clock in.
                                <a href="{{ route('contract.index') }}">Browse available jobs</a>
                            </div>
                        @else
                            <form action="{{ route('timesheet.clockIn') }}" method="POST" class="d-flex align-items-end">
                                @csrf
                                <div class="form-group mb-0 mr-3 flex-grow-1">
                                    <label for="contract_id">Select Job</label>
                                    <select name="contract_id" id="contract_id" class="form-control" required>
                                        <option value="">-- Select a job --</option>
                                        @foreach($acceptedJobs as $job)
                                            @if($job->contract)
                                                <option value="{{ $job->contract->id }}">
                                                    {{ $job->contract->title }} 
                                                    ({{ $job->contract->address ?? 'No address' }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="lat" id="clock-in-lat">
                                <input type="hidden" name="lng" id="clock-in-lng">
                                <button type="submit" class="btn btn-success btn-lg">
                                    🟢 Clock In
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Timesheet History -->
            <h4 class="mb-3">Timesheet History</h4>
            
            @if($timesheets->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h5 class="text-muted">No timesheets yet</h5>
                        <p class="text-muted">Clock in when you start work to track your hours.</p>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Job</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Hours</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timesheets as $timesheet)
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($timesheet->date)->format('d M Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($timesheet->date)->format('l') }}</small>
                                        </td>
                                        <td>
                                            @if($timesheet->contract)
                                                {{ $timesheet->contract->title }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $timesheet->clock_in ? $timesheet->clock_in->format('H:i') : '-' }}</td>
                                        <td>{{ $timesheet->clock_out ? $timesheet->clock_out->format('H:i') : '-' }}</td>
                                        <td>
                                            @if($timesheet->hours_worked)
                                                <strong>{{ number_format($timesheet->hours_worked, 2) }}h</strong>
                                            @else
                                                <span class="text-muted">In progress</span>
                                            @endif
                                        </td>
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
                                                {{ ucfirst($timesheet->status) }}
                                            </span>
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
                                            @if($timesheet->status == 'disputed' && $timesheet->dispute_reason)
                                                <br>
                                                <small class="text-danger">{{ Str::limit($timesheet->dispute_reason, 30) }}</small>
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

<script>
// Get location for clock in
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById('clock-in-lat').value = position.coords.latitude;
        document.getElementById('clock-in-lng').value = position.coords.longitude;
    });
}
</script>
@endsection

