@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">JOBS {{--<a href="{{ route('dashboard.contract.create') }}" class="btn btn-primary btn-sm float-right">ADD NEW</a>--}}</h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Type</th>
							<th>User Name</th>
							<th>Title</th>
							<th>Publish Date</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($contracts ?? [] as $contract)
							<tr>
								<td>{{ $contract->id }}</td>
								<td>{{ $contract->role->title }}</td>
								<td>{{ $contract->user->firstname ?? '' }} {{ $contract->user->lastname ?? '' }}</td>
								<td>{{ $contract->title }}</td>
								<td>@if($contract->created_at ?? false) {{ $contract->created_at->format('d M Y') }} @else N\A @endif</td>
								<td>
									<form action="{{ route('dashboard.contract.status', ['contract' => $contract->id])  }}" method="post">
										@csrf
										<select name="status" class="custom-select status-autoupate">
											@foreach(['pending', 'active'] as $s)
												<option value="{{ $s }}" @if($s == $contract->status) selected @endif>{{ ucfirst($s) }}</option>
											@endforeach
										</select>
									</form>
								</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="{{ route('dashboard.contract.edit', [ 'contract' => $contract->id ]) }}"
										   class="btn btn-primary btn-edit"
										>EDIT</a>
										<a href="{{ route('dashboard.contract.destroy', ['contract' => $contract->id]) }}"
										   class="btn btn-danger btn-delete"
										   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('contract-{{ $contract->id }}').submit();"
										>DELETE</a>
										<form id="contract-{{ $contract->id }}" action="{{ route('dashboard.contract.destroy', ['contract' => $contract->id]) }}" method="POST" class="d-none">
											@csrf
											@method('delete')
										</form>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
