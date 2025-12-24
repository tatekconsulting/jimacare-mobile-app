@extends('admin.template.layout')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fa fa-credit-card"></i> Payment Details #{{ $payment->id }}
    </h1>
    <div>
        <a href="{{ route('dashboard.timesheet-payments.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        @if($payment->stripe_payment_link_url)
            <a href="{{ $payment->stripe_payment_link_url }}" target="_blank" class="btn btn-success">
                <i class="fa fa-external-link"></i> View Payment Link
            </a>
        @endif
    </div>
</div>

<div class="row">
    <!-- Payment Information -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Client:</strong><br>
                        @if($payment->client)
                            {{ $payment->client->firstname }} {{ $payment->client->lastname ?? '' }}
                            <br>
                            <small class="text-muted">{{ $payment->client->email ?? '' }}</small>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Service Provider:</strong><br>
                        @if($payment->carer)
                            {{ $payment->carer->firstname }} {{ $payment->carer->lastname ?? '' }}
                            <br>
                            <small class="text-muted">{{ $payment->carer->role->title ?? 'N/A' }} | {{ $payment->carer->email ?? '' }}</small>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Period:</strong><br>
                        {{ $payment->period_start->format('M d, Y') }} - {{ $payment->period_end->format('M d, Y') }}
                        <br>
                        <small class="text-muted">{{ ucfirst($payment->period_type) }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge badge-lg 
                            @if($payment->status == 'paid') badge-success
                            @elseif($payment->status == 'pending' || $payment->status == 'link_sent') badge-warning
                            @elseif($payment->status == 'failed') badge-danger
                            @else badge-secondary
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <strong>Total Hours</strong><br>
                        <span class="h4">{{ number_format($payment->total_hours, 2) }}h</span>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <strong>Hourly Rate</strong><br>
                        <span class="h4">£{{ number_format($payment->hourly_rate, 2) }}</span>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <strong>Subtotal</strong><br>
                        <span class="h4">£{{ number_format($payment->subtotal, 2) }}</span>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <strong>Platform Fee</strong><br>
                        <span class="h4 text-info">£{{ number_format($payment->platform_fee, 2) }}</span>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <strong class="h3">Total Amount: <span class="text-success">£{{ number_format($payment->total_amount, 2) }}</span></strong>
                </div>
            </div>
        </div>

        <!-- Timesheets Included -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fa fa-clock-o"></i> Timesheets Included ({{ $timesheets->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($timesheets->isEmpty())
                    <p class="text-muted">No timesheets found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Hours</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timesheets as $timesheet)
                                    <tr>
                                        <td>{{ $timesheet->date ? $timesheet->date->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $timesheet->clock_in ? $timesheet->clock_in->format('H:i') : '-' }}</td>
                                        <td>{{ $timesheet->clock_out ? $timesheet->clock_out->format('H:i') : '-' }}</td>
                                        <td>{{ number_format($timesheet->hours_worked, 2) }}h</td>
                                        <td>£{{ number_format($timesheet->hourly_rate, 2) }}</td>
                                        <td>£{{ number_format($timesheet->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Details Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Payment Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Created:</strong><br>
                    {{ $payment->created_at->format('M d, Y H:i') }}
                </div>
                @if($payment->link_sent_at)
                    <div class="mb-3">
                        <strong>Link Sent:</strong><br>
                        {{ $payment->link_sent_at->format('M d, Y H:i') }}
                    </div>
                @endif
                @if($payment->paid_at)
                    <div class="mb-3">
                        <strong>Paid At:</strong><br>
                        {{ $payment->paid_at->format('M d, Y H:i') }}
                    </div>
                @endif
                @if($payment->stripe_payment_link_id)
                    <div class="mb-3">
                        <strong>Stripe Link ID:</strong><br>
                        <small class="text-muted">{{ $payment->stripe_payment_link_id }}</small>
                    </div>
                @endif
                @if($payment->stripe_payment_intent_id)
                    <div class="mb-3">
                        <strong>Payment Intent ID:</strong><br>
                        <small class="text-muted">{{ $payment->stripe_payment_intent_id }}</small>
                    </div>
                @endif
            </div>
        </div>

        @if($payment->notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notes</h6>
                </div>
                <div class="card-body">
                    <p>{{ $payment->notes }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

