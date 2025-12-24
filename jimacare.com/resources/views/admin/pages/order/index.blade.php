@extends('admin.template.layout')

@section('content')
	<div class="row mb-4">
		<!-- Statistics Cards -->
		<div class="col-xl-3 col-md-6 mb-4">
			<div class="card border-left-primary shadow h-100 py-2">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Payments</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_payments']) }}</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-receipt fa-2x text-gray-300"></i>
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
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid Payments</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['paid_payments']) }}</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
							<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Payments</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_payments']) }}</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-clock fa-2x text-gray-300"></i>
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
							<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($stats['total_revenue'], 2) }}</div>
						</div>
						<div class="col-auto">
							<i class="fas fa-pound-sign fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card mb-4">
		<div class="card-header py-3">
			<div class="row">
				<div class="col-md-6">
					<h6 class="m-0 font-weight-bold text-primary">TIMESHEET PAYMENTS</h6>
					<small class="text-muted">Platform Fees: £{{ number_format($stats['total_platform_fees'], 2) }}</small>
				</div>
				<div class="col-md-6 text-right">
					<a href="{{ route('orders.export', request()->query()) }}" class="btn btn-outline-primary btn-sm">
						<i class="fas fa-download"></i> Export CSV
					</a>
				</div>
			</div>
		</div>
		<div class="card-body">
			<!-- Filters -->
			<form method="GET" action="{{ route('dashboard.order.index') }}" class="mb-4">
				<div class="row">
					<div class="col-md-3">
						<label>Status</label>
						<select name="status" class="form-control">
							<option value="">All Statuses</option>
							<option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
							<option value="link_sent" {{ request('status') == 'link_sent' ? 'selected' : '' }}>Link Sent</option>
							<option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
							<option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
							<option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
						</select>
					</div>
					<div class="col-md-3">
						<label>Client</label>
						<select name="client_id" class="form-control">
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
						<select name="carer_id" class="form-control">
							<option value="">All Providers</option>
							@foreach($carers as $carer)
								<option value="{{ $carer->id }}" {{ request('carer_id') == $carer->id ? 'selected' : '' }}>
									{{ $carer->firstname }} {{ $carer->lastname }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<label>Search</label>
						<input type="text" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}">
					</div>
				</div>
				<div class="row mt-2">
					<div class="col-md-3">
						<label>Date From</label>
						<input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
					</div>
					<div class="col-md-3">
						<label>Date To</label>
						<input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
					</div>
					<div class="col-md-6">
						<label>&nbsp;</label>
						<div>
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-filter"></i> Filter
							</button>
							<a href="{{ route('dashboard.order.index') }}" class="btn btn-secondary">
								<i class="fas fa-times"></i> Clear
							</a>
						</div>
					</div>
				</div>
			</form>

			<!-- Payments Table -->
			<div class="table-responsive">
				<table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Client</th>
							<th>Service Provider</th>
							<th>Period</th>
							<th>Hours</th>
							<th>Rate</th>
							<th>Subtotal</th>
							<th>Platform Fee</th>
							<th>Total Amount</th>
							<th>Status</th>
							<th>Created</th>
							<th>Paid Date</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@forelse($payments as $payment)
							<tr>
								<td>{{ $payment->id }}</td>
								<td>
									@if($payment->client)
										<strong>{{ $payment->client->firstname }} {{ $payment->client->lastname }}</strong><br>
										<small class="text-muted">{{ $payment->client->email }}</small>
									@else
										<span class="text-muted">N/A</span>
									@endif
								</td>
								<td>
									@if($payment->carer)
										<strong>{{ $payment->carer->firstname }} {{ $payment->carer->lastname }}</strong><br>
										<small class="text-muted">{{ $payment->carer->role->title ?? 'N/A' }}</small>
									@else
										<span class="text-muted">N/A</span>
									@endif
								</td>
								<td>
									{{ $payment->period_start->format('d M Y') }} - {{ $payment->period_end->format('d M Y') }}<br>
									<small class="text-muted">{{ ucfirst($payment->period_type) }}</small>
								</td>
								<td>{{ number_format($payment->total_hours, 2) }}h</td>
								<td>£{{ number_format($payment->hourly_rate, 2) }}/hr</td>
								<td>£{{ number_format($payment->subtotal, 2) }}</td>
								<td>£{{ number_format($payment->platform_fee, 2) }}</td>
								<td><strong>£{{ number_format($payment->total_amount, 2) }}</strong></td>
								<td>
									@if($payment->status == 'paid')
										<span class="badge badge-success">Paid</span>
									@elseif($payment->status == 'pending')
										<span class="badge badge-warning">Pending</span>
									@elseif($payment->status == 'link_sent')
										<span class="badge badge-info">Link Sent</span>
									@elseif($payment->status == 'failed')
										<span class="badge badge-danger">Failed</span>
									@elseif($payment->status == 'cancelled')
										<span class="badge badge-secondary">Cancelled</span>
									@else
										<span class="badge badge-secondary">{{ ucfirst($payment->status) }}</span>
									@endif
								</td>
								<td>
									@if($payment->created_at)
										{{ $payment->created_at->format('d M Y') }}<br>
										<small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
									@else
										N/A
									@endif
								</td>
								<td>
									@if($payment->paid_at)
										{{ $payment->paid_at->format('d M Y H:i') }}
									@else
										<span class="text-muted">-</span>
									@endif
								</td>
								<td>
									<a href="{{ route('dashboard.timesheet-payments.show', ['payment' => $payment->id]) }}" 
									   class="btn btn-sm btn-primary" title="View Details">
										<i class="fas fa-eye"></i>
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="13" class="text-center py-4">
									<p class="text-muted mb-0">No payments found.</p>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			<div class="mt-3">
				{{ $payments->links() }}
			</div>
		</div>
	</div>

	@if(isset($legacyOrders) && $legacyOrders->count() > 0)
		<div class="card mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-secondary">LEGACY ORDERS (Old System)</h6>
				<small class="text-muted">These are from the old order system. Showing last 10 for reference.</small>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead>
							<tr>
								<th>ID</th>
								<th>Invoice ID</th>
								<th>Client</th>
								<th>Seller</th>
								<th>Price</th>
								<th>Status</th>
								<th>Created</th>
							</tr>
						</thead>
						<tbody>
							@foreach($legacyOrders as $order)
								<tr>
									<td>{{ $order->id }}</td>
									<td>{{ $order->invoice->id ?? 'N/A' }}</td>
									<td>{{ $order->client->firstname ?? '' }} {{ $order->client->lastname ?? '' }}</td>
									<td>{{ $order->seller->firstname ?? '' }} {{ $order->seller->lastname ?? '' }}</td>
									<td>£{{ number_format($order->payment->price ?? 0, 2) }}</td>
									<td>{{ ucfirst($order->status ?? '') }}</td>
									<td>{{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	@endif
@endsection
