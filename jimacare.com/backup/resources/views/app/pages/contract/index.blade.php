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
						<option value="">First Enter location than select radius</option>
						@foreach($radiuses as $key => $title)
							<option value="{{ $key }}"
									@if($key == request('radius')) selected @endif
							>{{ ucfirst($title) }}</option>
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
				@foreach($contracts as $contract)
					<div class="col-12 py-3 mb-3 bg-white">
						<div class="row">
							@if( (auth()->user()->role->slug ?? '') == 'admin')
								<div class="seller-left col-12 col-md-3">
									<div class="seller-img">
										<img class="img img-fluid seller-avatar" src="{{ asset($contract->user->profile) }}"
											 alt="{{ $contract->user->firstname ?? '' }}">
									</div>
								</div>
							@endif
							<div
								class="seller-center col-12 @if( (auth()->user()->role->slug ?? '') == 'admin') col-md-6 @else col-md-9 @endif text-center text-md-left">
								<h1>{{ $contract->user->name ?explode(' ',trim($contract->user->name))[0]: '' }}</h1>
								<p>
									<span class="fa fa-map-marker mr-2"></span>
									{{ $contract->user->city ?? '' }}, {{ $contract->user->country ?? '' }}
									, {{ isset($contract->user->postcode)?substr($contract->user->postcode,0,3): '' }}

									@if(auth()->user() && auth()->user()->role_id==1 && strlen($contract->company) > 0)
										<span class="profile-seperator">|</span>
										<span class="fa fa-building mr-2"></span> {{ $contract->company }}
									@endif
									@if(request('lat') && request('long'))
										<span class="profile-seperator">|</span>
										{{ $contract->miles ?? 0 }} Miles Away
									@endif
								</p>
								<div class="profile-badge-info">
									<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Verified & Approved</span>
									{{--<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Insured</span>--}}
								</div>
								<p class="mt-3">
									{{ $contract->desc ?? '' }}
								</p>
							</div>
							<div class="seller-right col-12 col-md-3 text-center text-md-right flex-right">
								{{--<div class="rating-section text-center text-md-right">
									<span class="rating raty readable" data-score="{{ $contract->user->reviews_avg ?? 0 }}"></span> ({{ $contract->user->reviews_count ?? 0 }})
								</div>--}}
								<div>
									<a href="{{ route('contract.show', ['contract' => $contract->id]) }}" class="btn btn-danger">
										<span class="fa fa-info mr-2"></span> Show Job
									</a>
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			<div class="row py-5">
				<div class="col-12">
					{{ $contracts->links('app.template.pagination') }}
				</div>
			</div>
		</div>
	</div>
@endsection
