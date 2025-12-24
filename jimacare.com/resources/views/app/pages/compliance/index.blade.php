@extends('app.template.layout-profile')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card shadow mb-4">
				<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					<h6 class="m-0 font-weight-bold text-primary">
						<i class="fa fa-shield-alt mr-2"></i>Compliance & Certification Tracking
					</h6>
				</div>
				<div class="card-body">
					{{-- Statistics Cards --}}
					<div class="row mb-4">
						<div class="col-md-3 mb-3">
							<div class="card border-left-primary shadow h-100 py-2">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
												Total Documents
											</div>
											<div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
										</div>
										<div class="col-auto">
											<i class="fa fa-file-alt fa-2x text-gray-300"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 mb-3">
							<div class="card border-left-success shadow h-100 py-2">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
												Valid
											</div>
											<div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['valid'] }}</div>
										</div>
										<div class="col-auto">
											<i class="fa fa-check-circle fa-2x text-gray-300"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 mb-3">
							<div class="card border-left-warning shadow h-100 py-2">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
												Expiring Soon
											</div>
											<div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expiring'] }}</div>
										</div>
										<div class="col-auto">
											<i class="fa fa-exclamation-triangle fa-2x text-gray-300"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3 mb-3">
							<div class="card border-left-danger shadow h-100 py-2">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-2">
											<div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
												Expired
											</div>
											<div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expired'] }}</div>
										</div>
										<div class="col-auto">
											<i class="fa fa-times-circle fa-2x text-gray-300"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					{{-- Expiring Soon Alerts --}}
					@if($expiringSoon->count() > 0)
					<div class="alert alert-warning mb-4">
						<h5 class="alert-heading">
							<i class="fa fa-exclamation-triangle mr-2"></i>Documents Expiring Soon
						</h5>
						<p class="mb-2">The following documents will expire within 30 days:</p>
						<ul class="mb-0">
							@foreach($expiringSoon as $doc)
							<li>
								<strong>{{ $doc->name }}</strong> - 
								Expires: {{ $doc->expiration->format('d/m/Y') }} 
								({{ $doc->expiration->diffForHumans() }})
								<a href="{{ route('documents') }}" class="ml-2">Update</a>
							</li>
							@endforeach
						</ul>
					</div>
					@endif

					{{-- Expired Documents Alerts --}}
					@if($expired->count() > 0)
					<div class="alert alert-danger mb-4">
						<h5 class="alert-heading">
							<i class="fa fa-times-circle mr-2"></i>Expired Documents
						</h5>
						<p class="mb-2">The following documents have expired and need to be renewed:</p>
						<ul class="mb-0">
							@foreach($expired as $doc)
							<li>
								<strong>{{ $doc->name }}</strong> - 
								Expired: {{ $doc->expiration->format('d/m/Y') }} 
								({{ $doc->expiration->diffForHumans() }})
								<a href="{{ route('documents') }}" class="ml-2">Renew</a>
							</li>
							@endforeach
						</ul>
					</div>
					@endif

					{{-- All Documents Table --}}
					<div class="table-responsive">
						<table class="table table-bordered" id="complianceTable" width="100%" cellspacing="0">
							<thead>
								<tr>
									<th>Document Name</th>
									<th>Expiration Date</th>
									<th>Status</th>
									<th>Days Remaining</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@forelse($documents as $doc)
								<tr>
									<td>{{ $doc->name }}</td>
									<td>
										@if($doc->expiration)
											{{ $doc->expiration->format('d/m/Y') }}
										@else
											<span class="text-muted">Not set</span>
										@endif
									</td>
									<td>
										@if($doc->compliance_status === 'valid')
											<span class="badge badge-success">Valid</span>
										@elseif($doc->compliance_status === 'expiring')
											<span class="badge badge-warning">Expiring Soon</span>
										@elseif($doc->compliance_status === 'expired')
											<span class="badge badge-danger">Expired</span>
										@else
											<span class="badge badge-secondary">No Expiry</span>
										@endif
									</td>
									<td>
										@if($doc->expiration)
											@if($doc->isExpired())
												<span class="text-danger">Expired {{ abs($doc->expiration->diffInDays(now())) }} days ago</span>
											@else
												<span class="text-success">{{ $doc->expiration->diffInDays(now()) }} days</span>
											@endif
										@else
											<span class="text-muted">-</span>
										@endif
									</td>
									<td>
										<a href="{{ route('documents') }}" class="btn btn-sm btn-primary">
											<i class="fa fa-edit"></i> Update
										</a>
									</td>
								</tr>
								@empty
								<tr>
									<td colspan="5" class="text-center text-muted py-4">
										No documents found. <a href="{{ route('documents') }}">Add documents</a>
									</td>
								</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<div class="mt-4">
						<a href="{{ route('documents') }}" class="btn btn-primary">
							<i class="fa fa-file-alt mr-2"></i>Manage Documents
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.border-left-primary {
		border-left: 0.25rem solid #4e73df !important;
	}
	.border-left-success {
		border-left: 0.25rem solid #1cc88a !important;
	}
	.border-left-warning {
		border-left: 0.25rem solid #f6c23e !important;
	}
	.border-left-danger {
		border-left: 0.25rem solid #e74a3b !important;
	}
</style>
@endsection

