@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">ORDERS</h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Invoice ID</th>
							<th>Client</th>
							<th>Seller</th>
							<th>Started Date</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($orders ?? [] as $order)
							<tr>
								<td>{{ $order->id }}</td>
								<td>{{ $order->invoice->id }}</td>
								<td>{{ $order->client->name ?? '' }}</td>
								<td>{{ $order->seller->name ?? '' }}</td>
								<td>@if($order->created_at ?? false) {{ $order->created_at->format('d M Y') }} @else N\A @endif</td>
								<td>{{ ucfirst($order->status ?? '') }}</td>
								<td>
									<a href="{{ route('order.show', [ 'order' => $order->id ]) }}" class="btn btn-primary">View</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
