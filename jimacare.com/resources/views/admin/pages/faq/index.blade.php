@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">FAQS <a href="{{ route('dashboard.faq.create') }}" class="btn btn-primary btn-sm float-right">ADD NEW</a></h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Type</th>
							<th>Title</th>
							<th>Detail</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($faqs ?? [] as $faq)
							<tr>
								<td>{{ $faq->id }}</td>
								<td>{{ $faq->role->title }}</td>
								<td>{{ $faq->title }}</td>
								<td>{{ substr($faq->desc, 0 , 80) }}</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="{{ route('dashboard.faq.edit', [ 'faq' => $faq->id ]) }}"
										   class="btn btn-primary btn-edit"
										>EDIT</a>
										<a href="{{ route('dashboard.faq.destroy', ['faq' => $faq->id]) }}"
										   class="btn btn-danger btn-delete"
										   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('faq-{{ $faq->id }}').submit();"
										>DELETE</a>
										<form id="faq-{{ $faq->id }}" action="{{ route('dashboard.faq.destroy', ['faq' => $faq->id]) }}" method="POST" class="d-none">
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
