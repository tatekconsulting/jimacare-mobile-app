@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>💳 Payment Details</h2>
                    <small class="text-muted">Payment #{{ $payment->id }}</small>
                </div>
                <div>
                    <a href="{{ route('timesheet-payments.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    @if($payment->status == 'link_sent' && $payment->stripe_payment_link_url)
                        <a href="{{ $payment->stripe_payment_link_url }}" target="_blank" class="btn btn-success">
                            <i class="fa fa-external-link"></i> Pay Now
                        </a>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Payment Information -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fa fa-info-circle"></i> Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Service Provider:</strong><br>
                                    @if($payment->carer)
                                        {{ $payment->carer->firstname }} {{ $payment->carer->lastname ?? '' }}
                                        <br>
                                        <small class="text-muted">{{ $payment->carer->role->title ?? 'N/A' }}</small>
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
                                    <strong>Total Hours:</strong><br>
                                    <span class="h5">{{ number_format($payment->total_hours, 2) }}h</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Hourly Rate:</strong><br>
                                    <span class="h5">£{{ number_format($payment->hourly_rate, 2) }}/hr</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Subtotal:</strong><br>
                                    <span class="h5">£{{ number_format($payment->subtotal, 2) }}</span>
                                </div>
                                @if($payment->platform_fee > 0)
                                    <div class="col-md-6 mb-3">
                                        <strong>Platform Fee:</strong><br>
                                        <span class="h5 text-info">£{{ number_format($payment->platform_fee, 2) }}</span>
                                    </div>
                                @endif
                                <div class="col-md-12">
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="h4 mb-0">Total Amount:</strong>
                                        <span class="h3 text-success mb-0">£{{ number_format($payment->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timesheets Included -->
                    <div class="card shadow">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-clock-o"></i> Timesheets Included ({{ $timesheets->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($timesheets->isEmpty())
                                <p class="text-muted">No timesheets found.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Clock In</th>
                                                <th>Clock Out</th>
                                                <th>Hours</th>
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

                <!-- Payment Status -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-info"></i> Payment Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
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
                            @if($payment->stripe_payment_link_url)
                                <div class="mb-3">
                                    <strong>Payment Link:</strong><br>
                                    <a href="{{ $payment->stripe_payment_link_url }}" target="_blank" class="btn btn-sm btn-success btn-block">
                                        <i class="fa fa-external-link"></i> Open Payment Link
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($payment->notes)
                        <div class="card shadow">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fa fa-sticky-note"></i> Notes</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $payment->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

