@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">POSTS <a href="{{ route('dashboard.post.create') }}" class="btn btn-primary btn-sm float-right">ADD NEW</a></h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Image</th>
							<th>Type</th>
							<th>Title</th>
							<th>Detail</th>
							<th>Publish At</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					@foreach($posts ?? [] as $post)
						<tr>
							<td>{{ $post->id }}</td>
							<td>
								@if($post->image ?? false)
									<img src="{{ asset($post->image ?? '') }}" alt="" height="40">
								@endif
							</td>
							<td>{{ $post->role->title }}</td>
							<td>{{ $post->title }}</td>
							<td>{!! substr($post->desc, 0 , 80) !!}</td>
							<td>@if($post->created_at ?? false) {{ $post->created_at->format('Y-m-d') }} @else N\A @endif</td>
							<td>
								<div class="btn-group btn-group-sm">
									<a href="{{ route('dashboard.post.edit', [ 'post' => $post->id ]) }}"
									   class="btn btn-primary btn-edit"
									>EDIT</a>
									<a href="{{ route('dashboard.post.destroy', ['post' => $post->id]) }}"
									   class="btn btn-danger btn-delete"
									   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('post-{{ $post->id }}').submit();"
									>DELETE</a>
										<form id="post-{{ $post->id }}" action="{{ route('dashboard.post.destroy', ['post' => $post->id]) }}" method="POST" class="d-none">
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
