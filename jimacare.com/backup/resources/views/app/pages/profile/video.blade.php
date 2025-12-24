@extends('app.template.layout-profile')

@section('content')
	<div class="about-you">
		<div class="step-wrap">
			<form method="POST" action="{{ route('video') }}" enctype="multipart/form-data" class="row about-form novalidate">
				@csrf

				<div class="col-12 pb-3">
					<h3 class="bg-white text-dark p-3">Upload Video</h3>
				</div>
				<div class="col-12">
					<div class="upload-label mx-auto text-center" style="width: 600px;">
						<div class="upload-video">
							<video class="w-100" src="{{ $profile->video ?? '' }}" controls muted autoplay></video>
						</div>
						<input type="file" name="video" id="video" class="d-none" accept="video/*"/>
						<progress class="d-none" value="0" max="100"></progress>
						<br>
						<a class="font-weight-bold pt-3 a-upload-video" href="javascript:void(0)">Upload Video <small>(Max: 8Mb)</small></a>
						@if($profile->video)
							&nbsp;&nbsp;|&nbsp;&nbsp;
							<a id="remove-video" class="font-weight-bold pt-3" href="javascript:void(0)">Remove Video</a>
						@endif
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
