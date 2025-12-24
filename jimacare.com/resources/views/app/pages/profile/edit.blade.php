@extends('app.template.layout')

@section('content')
	<div class="full-width mt-4 profile-management">
		<div class="container">
			<div class="row">
				<div class="col-12 py-3">
					@if ($errors->any())
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
				</div>
				<div class="col-12">
					<div class="about-you">
						<div class="step-wrap">
							<form method="POST" action="{{ route('profile.store') }}" enctype="multipart/form-data" id="about1" class="row about-form novalidate">
								@csrf
								@include('app.pages.profile.avatar', compact('profile'))
								<div class="col-12 col-md-9">
									<div class="row">
										@include('app.pages.profile.basic', compact('profile'))

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


									</div>


								</div>
								<div class="form-group col-12">
									<button class="btn btn-primary float-right px-5" type="submit">Submit <span class="fa fa-long-arrow-right ml-2"></span></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
