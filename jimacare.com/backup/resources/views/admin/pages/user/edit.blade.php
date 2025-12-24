@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">EDIT USER <a href="{{ route('dashboard.user.index') }}" class="btn btn-primary btn-sm float-right">MANAGE ALL</a></h6>
		</div>
		<div class="card-body">
			<form class="row" action="{{ route('dashboard.user.update', [ 'user' => $profile->id]) }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="col-12 mb-3">
					<label for="profile">Profile Image</label>

					<div class="upload-wrap bg-light text-center">
						<img class="image-ph" src="{{ asset($profile->profile ?? '' ) }}">
					</div>
					<input class="upload-input d-none" type="file" name="profile">
				</div>
				@error('profile')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror

				@include('app.pages.profile.basic', compact('profile'))
				@include('app.pages.profile.documents-list', compact('documents'))
				@include('app.pages.profile.languages', compact('profile'))

				@include('app.pages.profile.service-type', compact('profile'))
				@include('app.pages.profile.years_experience', compact('profile'))
				@include('app.pages.profile.experiences', compact('profile'))
				@include('app.pages.profile.skills', compact('profile'))


				@include('app.pages.profile.working-days', compact('profile'))
				@include('app.pages.profile.working-time', compact('profile'))

				@include('app.pages.profile.childminder-fee', compact('profile'))

				@include('app.pages.profile.interests', compact('profile'))

				@include('app.pages.profile.educations', compact('profile'))

				@include('app.pages.profile.infos', compact('profile'))

				@include('app.pages.profile.references', compact('profile'))

				@include('app.pages.profile.dbs', compact('profile'))

				<div class="form-group col-12">
					<label>Badges</label>
					<div>
						<label class="btn btn-outline-primary" data-toggle="button"
							   @if($profile->approved) aria-pressed="true" @else aria-pressed="false" @endif
						>
							<input type="checkbox" name="approved" value="1"
								   id="approved" class="approved"
								   @if($profile->approved) checked @endif
							/> Verified & Approved
						</label>

						<label class="btn btn-outline-primary" data-toggle="button"
							   @if($profile->insured) aria-pressed="true" @else aria-pressed="false" @endif
						>
							<input type="checkbox" name="insured" value="1"
								   id="insured" class="insured"
								   @if($profile->insured) checked @endif
							/> Insured
						</label>

						<label class="btn btn-outline-primary" data-toggle="button"
							   @if($profile->vaccinated) aria-pressed="true" @else aria-pressed="false" @endif
						>
							<input type="checkbox" name="vaccinated" value="1"
								   id="approved" class="vaccinated"
								   @if($profile->vaccinated) checked @endif
							/> Vaccinated
						</label>
					</div>
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
