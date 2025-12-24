@extends('app.template.layout')

@section('content')
	<div class="blog-head mb-3">
		<div class="container py-3 bg-white">
			<form method="get" class="row mx-n1 location-autofill">
				<div class="form-group col-12 col-md-3 px-1">
					<label for="type">I'm looking for</label>
					<select name="type"
							id="type" class="type type_filter custom-select"
					>
						<option value="">Please Select</option>
						@foreach($roles as $role)
							<option value="{{ $role->id }}"
									@if($role->id == request('type')) selected @endif
							>{{ ucfirst($role->title) }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-12 col-md px-1">
					<label for="radius">Within</label>
					@php
						$radiuses = [
							1 => '1 Mile',
							2 => '2 Miles',
							3 => '3 Miles',
							4 => '4 Miles',
							5 => '5 Miles',
							7 => '7 Miles',
							10 => '10 Miles',
						];
					@endphp
					<select name="radius"
							id="radius" class="radius custom-select"
					>
						@foreach($radiuses as $key => $title)
							<option value="{{ $key }}" @if($key == (request('radius')??5)) selected @endif>{{ ucfirst($title) }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-12 col-md px-1">
					<label for="address">Location</label>
					<input type="text" name="address" value="{{ request('address') }}"
						   id="address" class="address form-control"
						   placeholder="Location"
					/>
				</div>
				<input type="hidden" name="lat" class="lat" value="{{ request('lat') }}" />
				<input type="hidden" name="long" class="long" value="{{ request('long') }}" />

				<div class="form-group col-12 col-md-4 px-1">
					<label for="type">Filter</label>
					<select name="experience"
							id="experience" class="experience experience_filter custom-select"
					>
						<option value="">All Results</option>
						@foreach($experiences as $experience)
							<option value="{{ $experience->id }}" data-type="{{ $experience->role_id }}"
									@if( $experience->id == request('experience') ) selected @endif
									@if( request('type') != $experience->role_id) class="d-none" @endif
							>{{ ucfirst($experience->title) }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-12 col-md px-1">
					<label for="postcode" class="d-none d-md-block">&nbsp;&nbsp;&nbsp;</label>
					<button class="btn btn-outline-primary btn-block" type="submit">Search</button>
				</div>
			</form>
		</div>
	</div>

	<div class="seller-listing">
		<div class="container">
			<div class="row seller-listing">
				@foreach($users as $user)
					<div class="col-12 py-3 mb-3 bg-white">
						<div class="row">
							<div class="seller-left col-12 col-md-auto">
								<div class="seller-img mx-auto" style="width: 200px; height: 200px;">
									<img class="img img-fluid seller-avatar" style="width: 100%; height: 100%;" src="{{ asset($user->profile ?? 'img/undraw_profile.svg') }}" alt="{{ $user->firstname }} {{ $user->lastname[0] ?? '' }}">
								</div>
							</div>
							<div class="seller-center col-12 col-md text-center text-md-left">
								<h1>{{ $user->name ?? '' }}</h1>
								<p>
									<span class="fa fa-map-marker mr-2"></span>
									{{ $user->city ?? '' }}, {{ $user->country ?? '' }}, {{ $user->postal ?? '' }}

									{{--@if($user->dob ?? false)
										<span class="profile-seperator">|</span>
										Age {{ \Carbon\Carbon::parse($user->dob)->age ?? '' }} Years
									@endif--}}

									@if(request('lat') && request('long'))
										<span class="profile-seperator">|</span>
										{{ $user->miles ?? 0 }} Miles Away
									@endif
									<span class="profile-seperator">|</span>
									Experience {{ $user->years_experience ?? 0 }} Years
								</p>
								<div class="profile-badge-info">
									@if($user->approved ?? false)
										<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Verified & Approved</span>
									@endif

									@if($user->insured ?? false)
										<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Insured</span>
									@endif

									@if($user->vaccinated ?? false)
										<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Vaccinated</span>
									@endif

								</div>
								@if($user->role->seller == true)
									<div class="profile-badge-info">
										@if($user->role_id == 3)
											@foreach($user->availabilities as $avail)
												<span class="profile-badge bg-success info text-white">
													{{ $avail->type->title }}: £{{ $avail->charges }} Per Hour
												</span>
											@endforeach
										@elseif( $user->fee ?? false)
											<span class="profile-badge bg-success info text-white">
												Hourly Charges: £{{ $user->fee ?? 10 }} /hr
											</span>
										@endif
									</div>
								@endif
								<p class="mt-3">
									{{ $user->info ?? '' }}
								</p>
							</div>
							<div class="seller-right col-12 col-md-3 text-center text-md-right">
								<div class="rating-section text-center text-md-right">
									<span class="rating raty readable" data-score="{{ $user->reviews_avg ?? 0 }}"></span> ({{ $user->reviews_count ?? 0 }})
								</div>
								<div>
									<a href="{{ route('seller.show', ['user' => $user->id]) }}" class="btn btn-danger">
										<span class="fa fa-user mr-2"></span> View Profile
									</a>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			<div class="row py-5">
				<div class="col-12">
					{{ $users->links('app.template.pagination') }}
				</div>
			</div>
		</div>
	</div>
@endsection
