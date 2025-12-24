@extends('app.template.layout-profile')

@section('content')
	<div class="about-you">
		<div class="step-wrap">
			<div class="wizard">
				<div class="wizard-inner">
					<div class="connecting-line"></div>
					<ul class="nav nav-tabs" role="tablist">
						<li @if(!request('tab') || request('tab')=='basic') class="active" @endif>
							<a href="{{route('profile')}}?tab=basic"><span class="round-tab">1 </span> <i>Basic Info</i></a>
						</li>
						<li @if(request('tab')=='pricing') class="active" @endif>
							<a href="{{route('profile')}}?tab=pricing"><span class="round-tab">2</span>
								<i>Languages {{$profile->role_id!=2?" & Pricing":""}}</i></a>
						</li>
						@if(!in_array($profile->role_id,[1,2]))
							<li @if(request('tab')=='experiences') class="active" @endif>
								<a href="{{route('profile')}}?tab=experiences"><span class="round-tab">3</span> <i>Experiences</i></a>
							</li>
							<li @if(request('tab')=='references') class="active" @endif>
								<a href="{{route('profile')}}?tab=references"><span class="round-tab">4</span> <i>References</i></a>
							</li>
						@endif
					</ul>
				</div>

				<form method="POST" action="{{ route('profile.store') }}" enctype="multipart/form-data" id="about1" class="row about-form novalidate">
					@csrf
					@if(!request('tab') || request('tab')=='basic')
						<h4 class="w-100 my-3">Basic Info</h4>
						<input type="hidden" name="type" value="basic">
						@include('app.pages.profile.basic', compact('profile'))

					@elseif(request('tab')=='pricing')
						<h4 class="w-100 my-3">Languages {{$profile->role_id!==2?" & Pricing":""}}</h4>
						<input type="hidden" name="type" value="pricing">
						@include('app.pages.profile.languages', compact('profile'))
						@include('app.pages.profile.service-type', compact('profile'))
						@include('app.pages.profile.childminder-fee', compact('profile'))
						@include('app.pages.profile.working-days', compact('profile'))
						@include('app.pages.profile.working-time', compact('profile'))

					@elseif(!in_array($profile->role_id,[1,2]) && request('tab')=='experiences')
						<h4 class="w-100 my-3">Experiences</h4>
						<input type="hidden" name="type" value="experiences">
						@include('app.pages.profile.years_experience', compact('profile'))
						@include('app.pages.profile.experiences', compact('profile'))
						@include('app.pages.profile.skills', compact('profile'))
						@include('app.pages.profile.interests', compact('profile'))
						@include('app.pages.profile.educations', compact('profile'))
						@include('app.pages.profile.infos', compact('profile'))

					@elseif(!in_array($profile->role_id,[1,2]) && request('tab')=='references')
						<h4 class="w-100 my-3">References</h4>
						<input type="hidden" name="type" value="references">
						@include('app.pages.profile.references', compact('profile'))
						@include('app.pages.profile.dbs', compact('profile'))

					@endif

					<div class="w-100 mt-3">
						<ul class="list-inline d-flex float-right">
							@if(request('tab')=='pricing' || request('tab')=='experiences' || request('tab')=='references')
								@if((request('tab')=='pricing'))
									<li><a class="btn btn-dark mx-2" href="{{route('profile')."?tab=basic"}}">Back</a></li>
								@elseif((request('tab')=='experiences'))
									<li><a class="btn btn-dark mx-2" href="{{route('profile')."?tab=pricing"}}">Back</a></li>
								@elseif((request('tab')=='references'))
									<li><a class="btn btn-dark mx-2" href="{{route('profile')."?tab=experiences"}}">Back</a></li>
								@endif
							@endif
							@if(((request('tab')=='pricing') && in_array($profile->role_id,[1,2])) || (request('tab')=='references'))
								<li>
									<button type="submit" class="btn btn-primary mx-2">Finish</button>
								</li>
							@else
								<li>
									<button type="submit" class="btn btn-primary mx-2">Next</button>
								</li>
							@endif
						</ul>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
