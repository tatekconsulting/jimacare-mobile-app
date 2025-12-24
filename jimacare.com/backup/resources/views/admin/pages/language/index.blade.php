@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">LANGUAGES <a href="{{ route('dashboard.language.create') }}" class="btn btn-primary btn-sm float-right">ADD NEW</a></h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Title</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($languages ?? [] as $language)
							<tr>
								<td>{{ $language->id }}</td>
								<td>{{ $language->title }}</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="{{ route('dashboard.language.edit', [ 'language' => $language->id ]) }}"
										   class="btn btn-primary btn-edit"
										>EDIT</a>
										<a href="{{ route('dashboard.language.destroy', ['language' => $language->id]) }}"
										   class="btn btn-danger btn-delete"
										   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('language-{{ $language->id }}').submit();"
										>DELETE</a>
										<form id="language-{{ $language->id }}" action="{{ route('dashboard.language.destroy', ['language' => $language->id]) }}" method="POST" class="d-none">
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
