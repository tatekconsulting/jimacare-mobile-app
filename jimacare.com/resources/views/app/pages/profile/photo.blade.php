@extends('app.template.layout-profile')

@section('content')
	<div class="about-you">
		<div class="step-wrap">
			<form method="POST" action="{{ route('photo') }}" enctype="multipart/form-data" class="row about-form novalidate">
				@csrf

				<div class="col-12 pb-3">
					<h3 class="bg-white text-dark p-3">Upload Photo</h3>
				</div>
				<div class="col-12">
					<div class="upload-label mx-auto text-center">
						<div class="upload-img">
							<img src="{{ $profile->profile ?? asset('img/undraw_profile.svg') }}" alt="" >
						</div>
						<input type="file" name="profile"
							   id="profile" class="d-none"
							   accept="image/*"
						/>
						<a class="font-weight-bold d-block pt-3 a-upload-photo" href="javascript:void(0)">Upload Photo</a>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
