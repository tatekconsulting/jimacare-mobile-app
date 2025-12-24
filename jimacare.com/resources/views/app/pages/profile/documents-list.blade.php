<div class="col-12 mb-5">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h3 class="bg-white text-dark p-3 mb-0">Uploaded Document</h3>
		<a href="{{ route('compliance.index') }}" class="btn btn-sm btn-info">
			<i class="fa fa-shield-alt mr-2"></i>Compliance Dashboard
		</a>
	</div>
	<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
			<tr>
				<th>Name</th>
				<th>Expiration Date</th>
				<th>Status</th>
				<th>Actions</th>
			</tr>
			</thead>
			<tbody>
			@foreach($documents ?? [] as $doc)
				@php
					$doc->updateComplianceStatus();
				@endphp
				<tr class="{{ $doc->compliance_status === 'expired' ? 'table-danger' : ($doc->compliance_status === 'expiring' ? 'table-warning' : '') }}">
					<td>{{ $doc->name }}</td>
					<td>
						@if($doc->expiration)
							{{ $doc->expiration->format('Y-m-d') }}
							@if($doc->isExpiringSoon())
								<br><small class="text-warning">
									<i class="fa fa-exclamation-triangle"></i> Expires in {{ $doc->expiration->diffInDays(now()) }} days
								</small>
							@elseif($doc->isExpired())
								<br><small class="text-danger">
									<i class="fa fa-times-circle"></i> Expired {{ abs($doc->expiration->diffInDays(now())) }} days ago
								</small>
							@endif
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
					<td class="text-right">
						<a href="{{ route('document.show', ['document' => $doc->id]) }}" class="btn btn-sm btn-primary">
							<i class="fa fa-download"></i> Download
						</a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
</div>
