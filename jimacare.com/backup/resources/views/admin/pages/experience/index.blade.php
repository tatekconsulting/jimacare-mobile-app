@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">EXPERIENCES <a href="{{ route('dashboard.experience.create') }}" class="btn btn-primary btn-sm float-right">ADD NEW</a></h6>
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
						@foreach($experiences ?? [] as $experience)
							<tr>
								<td>{{ $experience->id }}</td>
								<td>{{ $experience->role->title }}</td>
								<td>{{ $experience->title }}</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="{{ route('dashboard.experience.edit', [ 'experience' => $experience->id ]) }}"
										   class="btn btn-primary btn-edit"
										>EDIT</a>
										<a href="{{ route('dashboard.experience.destroy', ['experience' => $experience->id]) }}"
										   class="btn btn-danger btn-delete"
										   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('experience-{{ $experience->id }}').submit();"
										>DELETE</a>
										<form id="experience-{{ $experience->id }}" action="{{ route('dashboard.experience.destroy', ['experience' => $experience->id]) }}" method="POST" class="d-none">
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
