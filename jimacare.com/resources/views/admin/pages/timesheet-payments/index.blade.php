@extends('admin.template.layout')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fa fa-credit-card"></i> Timesheet Payments Management
    </h1>
    <div>
        <button class="btn btn-primary" data-toggle="modal" data-target="#generatePaymentModal">
            <i class="fa fa-plus"></i> Generate Payment Links
        </button>
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
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Payments</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPayments) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingPayments) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($paidPayments) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($totalRevenue, 2) }}</div>
                <small class="text-muted">Platform Fees: £{{ number_format($totalPlatformFees, 2) }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fa fa-list"></i> All Payments
        </h6>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('dashboard.timesheet-payments.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="link_sent" {{ request('status') == 'link_sent' ? 'selected' : '' }}>Link Sent</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label>Service Provider</label>
                    <select name="carer_id" class="form-control form-control-sm">
                        <option value="">All Providers</option>
                        @foreach($carers as $carer)
                            <option value="{{ $carer->id }}" {{ request('carer_id') == $carer->id ? 'selected' : '' }}>
                                {{ $carer->firstname }} {{ $carer->lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Service Provider</th>
                        <th>Period</th>
                        <th>Hours</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>
                                @if($payment->client)
                                    {{ $payment->client->firstname }} {{ substr($payment->client->lastname ?? '', 0, 1) }}.
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->carer)
                                    {{ $payment->carer->firstname }} {{ substr($payment->carer->lastname ?? '', 0, 1) }}.
                                    <br>
                                    <small class="text-muted">{{ $payment->carer->role->title ?? 'N/A' }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                {{ $payment->period_start->format('M d') }} - {{ $payment->period_end->format('M d, Y') }}
                                <br>
                                <small class="text-muted">{{ ucfirst($payment->period_type) }}</small>
                            </td>
                            <td><strong>{{ number_format($payment->total_hours, 2) }}h</strong></td>
                            <td>
                                <strong>£{{ number_format($payment->total_amount, 2) }}</strong>
                                @if($payment->platform_fee > 0)
                                    <br>
                                    <small class="text-muted">Fee: £{{ number_format($payment->platform_fee, 2) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if($payment->status == 'paid') badge-success
                                    @elseif($payment->status == 'pending' || $payment->status == 'link_sent') badge-warning
                                    @elseif($payment->status == 'failed') badge-danger
                                    @else badge-secondary
                                    @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('dashboard.timesheet-payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i>
                                </a>
                                @if($payment->stripe_payment_link_url)
                                    <a href="{{ $payment->stripe_payment_link_url }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fa fa-external-link"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $payments->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Generate Payment Modal -->
<div class="modal fade" id="generatePaymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('dashboard.timesheet-payments.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Payment Links</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-control" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->firstname }} {{ $client->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Service Provider (Optional)</label>
                        <select name="carer_id" class="form-control">
                            <option value="">All Service Providers</option>
                            @foreach($carers as $carer)
                                <option value="{{ $carer->id }}">{{ $carer->firstname }} {{ $carer->lastname }} ({{ $carer->role->title ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Leave empty to generate for all providers</small>
                    </div>
                    <div class="form-group">
                        <label>Period Type <span class="text-danger">*</span></label>
                        <select name="period_type" class="form-control" required>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Period Start (Optional)</label>
                                <input type="date" name="period_start" class="form-control">
                                <small class="text-muted">Leave empty for current period</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Period End (Optional)</label>
                                <input type="date" name="period_end" class="form-control">
                                <small class="text-muted">Leave empty for current period</small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> This will generate payment links for approved timesheets in the selected period. Payment links will be sent to the client via notification.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Payment Links</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

