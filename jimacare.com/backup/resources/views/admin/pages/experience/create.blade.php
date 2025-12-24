@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">CREATE EXPERIENCE <a href="{{ route('dashboard.experience.index') }}" class="btn btn-primary btn-sm float-right">MANAGE ALL</a></h6>
		</div>
		<div class="card-body">
			<form class="row" action="{{ route('dashboard.experience.store') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="col-12 form-group">
					<label for="title">Title</label>
					<input type="text" name="title" value="{{ old('title') }}"
						id="" class="form-control"
						required autofocus
					/>
					@error('title')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="col-12 form-group">
					<label for="type">Type</label>
					<select name="type"
						id="" class="custom-select"
						required
					>
						<option value="">Select Type</option>
						@foreach($roles as $role)
							<option value="{{ $role->id }}" @if(old('type') == $role->id) selected @endif>{{ $role->title }}</option>
						@endforeach
					</select>
					@error('type')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="col-12 form-group">
					<button type="submit"
							class="btn btn-primary btn-block"
					>
						PUBLISH
					</button>
				</div>


			</form>

		</div>
	</div>
@endsection
