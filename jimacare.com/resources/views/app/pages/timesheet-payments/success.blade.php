@extends('app.template.layout-profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fa fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h2 class="text-success mb-3">Payment Successful!</h2>
                    <p class="lead">Thank you for your payment.</p>
                    
                    @if($payment)
                        <div class="alert alert-info mt-4">
                            <strong>Payment Details:</strong><br>
                            Amount: £{{ number_format($payment->total_amount, 2) }}<br>
                            Period: {{ $payment->period_start->format('M d') }} - {{ $payment->period_end->format('M d, Y') }}<br>
                            Service Provider: {{ $payment->carer->firstname ?? 'N/A' }} {{ substr($payment->carer->lastname ?? '', 0, 1) }}.
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('timesheet-payments.index') }}" class="btn btn-primary">
                            <i class="fa fa-list"></i> View Payment History
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fa fa-home"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

