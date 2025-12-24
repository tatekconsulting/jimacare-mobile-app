@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">SKILLS <a href="{{ route('dashboard.skill.create') }}" class="btn btn-primary btn-sm float-right">ADD NEW</a></h6>
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
						@foreach($skills ?? [] as $skill)
							<tr>
								<td>{{ $skill->id }}</td>
								<td>{{ $skill->role->title }}</td>
								<td>{{ $skill->title }}</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="{{ route('dashboard.skill.edit', [ 'skill' => $skill->id ]) }}"
										   class="btn btn-primary btn-edit"
										>EDIT</a>
										<a href="{{ route('dashboard.skill.destroy', ['skill' => $skill->id]) }}"
										   class="btn btn-danger btn-delete"
										   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('skill-{{ $skill->id }}').submit();"
										>DELETE</a>
										<form id="skill-{{ $skill->id }}" action="{{ route('dashboard.skill.destroy', ['skill' => $skill->id]) }}" method="POST" class="d-none">
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
