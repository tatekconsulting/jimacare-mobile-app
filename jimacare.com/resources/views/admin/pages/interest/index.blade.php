@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">INTERESTS <a href="{{ route('dashboard.interest.create') }}" class="btn btn-primary btn-sm float-right">ADD NEW</a></h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Type</th>
							<th>Title</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($interests ?? [] as $interest)
							<tr>
								<td>{{ $interest->id }}</td>
								<td>{{ $interest->role->title }}</td>
								<td>{{ $interest->title }}</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="{{ route('dashboard.interest.edit', [ 'interest' => $interest->id ]) }}"
										   class="btn btn-primary btn-edit"
										>EDIT</a>
										<a href="{{ route('dashboard.interest.destroy', ['interest' => $interest->id]) }}"
										   class="btn btn-danger btn-delete"
										   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('interest-{{ $interest->id }}').submit();"
										>DELETE</a>
										<form id="interest-{{ $interest->id }}" action="{{ route('dashboard.interest.destroy', ['interest' => $interest->id]) }}" method="POST" class="d-none">
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
