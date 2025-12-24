@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">EDIT LANGUAGE <a href="{{ route('dashboard.language.index') }}" class="btn btn-primary btn-sm float-right">MANAGE ALL</a></h6>
		</div>
		<div class="card-body">
			<form class="row" action="{{ route('dashboard.language.update', [ 'language' => $language->id]) }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="col-12 form-group">
					<label for="title">Title</label>
					<input type="text" name="title" value="{{ $language->title }}"
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
					<button type="submit"
							class="btn btn-primary btn-block"
					>
						UPDATE
					</button>
				</div>
			</form>

		</div>
	</div>
@endsection
