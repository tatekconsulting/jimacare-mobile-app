@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<div class="container py-3 bg-white">
				<form method="get" class="row mx-n1">
					<div class="form-group col-12 col-md-3 px-1">
						<label for="name">Name or Phone</label>
						<input type="text" name="name" id="name" class="form-control" value="{{request('name')}}"/>
					</div>
					<div class="form-group col-12 col-md-3 px-1">
						<label for="type">Type</label>
						<select name="type" id="type" class="custom-select">
							<option value="">All</option>
							@foreach($roles as $role)
								<option value="{{ $role->id }}" @if($role->id == request('type')) selected @endif >{{ ucfirst($role->title) }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-12 col-md-3 px-1">
						<label for="type">Status</label>
						<select name="status" class="custom-select">
							<option value="">All</option>
							<option value="pending" @if(request('status')=='pending') selected @endif>Pending</option>
							<option value="review" @if(request('status')=='review') selected @endif>Review</option>
							<option value="active" @if(request('status')=='active') selected @endif>Active</option>
							<option value="block" @if(request('status')=='block') selected @endif>Block</option>
						</select>
					</div>
					<div class="form-group col-12 col-md px-1">
						<label for="postcode" class="d-none d-md-block">&nbsp;&nbsp;&nbsp;</label>
						<button class="btn btn-outline-primary btn-block" type="submit">Search</button>
					</div>
				</form>
			</div>
			<h6 class="m-0 font-weight-bold text-primary">USERS</h6>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" width="100%" cellspacing="0">
					<thead>
					<tr>
						<th>ID</th>
						<th>Avatar</th>
						<th>Type</th>
						<th>Full Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Member Since</th>
						<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($users ?? [] as $user)
							<tr>
								<td>{{ $user->id }}</td>
								<td>
									@if($user->profile ?? false)
										<img src="{{ asset($user->profile ?? '') }}" alt="" height="40">
									@endif
								</td>
								<td>{{ $user->role->title }}</td>
								<td>{{ $user->firstname }} {{ $user->lastname }}</td>
								<td>{{ $user->email }}</td>
								<td>{{ $user->phone }}</td>
								<td>@if($user->created_at ?? false) {{ $user->created_at->format('d M Y') }} @else N\A @endif</td>
								<td>
									<form action="{{ route('dashboard.user.status', ['user' => $user->id])  }}" method="post">
										@csrf
										<select name="status" class="custom-select status-autoupate">
											@foreach(['pending', 'review', 'active', 'block'] as $s)
												<option value="{{ $s }}" @if($s == $user->status) selected @endif>{{ ucfirst($s) }}</option>
											@endforeach
										</select>
									</form>
								</td>
								<td>
									<div class="btn-group btn-group-sm">
										<a href="{{ route('dashboard.user.edit', [ 'user' => $user->id ]) }}"
										   class="btn btn-primary btn-edit"
										>EDIT</a>
										<a href="{{ route('dashboard.user.destroy', ['user' => $user->id]) }}"
										   class="btn btn-danger btn-delete"
										   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete?') == true) document.getElementById('user-{{ $user->id }}').submit();"
										>DELETE</a>
										<form id="user-{{ $user->id }}" action="{{ route('dashboard.user.destroy', ['user' => $user->id]) }}" method="POST"
											  class="d-none">
											@csrf
											@method('delete')
										</form>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{{$users->appends($_GET)->links('pagination.default')}}
			</div>
		</div>
	</div>
@endsection
