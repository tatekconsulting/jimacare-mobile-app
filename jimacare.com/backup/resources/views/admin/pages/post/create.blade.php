@extends('admin.template.layout')

@section('content')
	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">CREATE POST <a href="{{ route('dashboard.post.index') }}" class="btn btn-primary btn-sm float-right">MANAGE
					ALL</a></h6>
		</div>
		<div class="card-body">
			<form class="row" action="{{ route('dashboard.post.store') }}" method="POST" enctype="multipart/form-data">
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


				<div class="col-12 mb-3">
					<label for="type">Thumbnail Image</label>
					<div class="upload-wrap bg-light text-center">
						<div class="placeholder" style="padding: 120px 50px;">Upload Image</div>
					</div>
					<input class="upload-input d-none" type="file" name="image">
				</div>
				@error('image')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror

				<div class="col-12 mb-3">
					<label for="type">Banner Image</label>
					<div class="upload-wrap bg-light text-center">
						<div class="placeholder" style="padding: 120px 50px;">Upload Image</div>
					</div>
					<input class="upload-input d-none" type="file" name="banner">
				</div>
				@error('banner')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror

				<div class="col-12 form-group">
					<label for="desc">Description</label>
					<textarea name="desc" rows="4"
						   id="" class="form-control"
						   placeholder="Description"
						   required
					>{{ old('desc') }}</textarea>
					@error('desc')
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
@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
	<script>
		$(document).ready(function () {
			$('textarea').summernote();
		});
	</script>
@endpush
