@extends('app.template.layout')

@section('content')
	<div class="about-banner">
		<div class="banner-img">
			<img class="img-responsive" src="{{ asset('img/aboutbanner.png') }}" alt="">
		</div>
		<div class="container pt-3">
			<div class="row">
				<div class="col-md-8">
					<h1 class="mt-3">Create a {{ strtolower($role->title) }} job ad with us</h1>
				</div>
			</div>
		</div>
	</div>


	<div class="requirements-form pt-3 pb-5 mb-3">
		<div class="container">
			<form method="POST" action="{{ route('contract.store', ['type' => $role->slug]) }}" class="row">
				@csrf

				@if ($errors->any())
					<div class="col-12">
						<div class="alert alert-danger w-100">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					</div>
				@endif

				<div class="form-group col-12">
					<label for="title">Title</label>
					<input type="text" name="title" value="{{ old('title') }}"
						   id="title" class="title form-control"
						   placeholder="Title" min="4" required autofocus
					/>
				</div>

				<div class="form-group col-12">
					<label for="company">Company Name (If any)</label>
					<input type="text" name="company" value="{{ old('company') }}"
						   id="title" class="title form-control"
						   placeholder="Company Name (If any)" min="4"
					/>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="start_type">When would you like to start?</label>
					<select name="start_type"
							id="start_type" class="start_type custom-select"
							required
					>
						@php $starts = [
							'immediately' => 'Immediately',
							'not-sure'  => 'Not Sure',
							'specific-date' => 'Specific Date',
						]; @endphp
						@foreach($starts as $key => $val)
							<option value="{{ $key }}"
									@if($key == old('start_type')) selected @endif
							>{{ $val }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="start_date">Start Date</label>
					<input type="date" name="start_date" value="{{ old('start_date') }}"
						   id="period_date" class="period_date form-control"
						   placeholder="Period End Date" required
					>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="end_type">When would you like to end?</label>
					<select name="end_type"
							id="end_type" class="end_type custom-select"
							required
					>
						@php $ends = [
							'fixed-period' => 'Fixed Period',
							'on-going' => 'On Going'
						]; @endphp
						@foreach($ends as $key => $val)
							<option value="{{ $key }}"
									@if($key == old('end_type')) selected @endif
							>{{ $val }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="end_date">End Date</label>
					<input type="date" name="end_date" value="{{ old('end_date') }}"
						   id="end_date" class="end_date form-control"
						   placeholder="End Date" required
					>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="start_time">Arrival Time</label>
					<input type="time" name="start_time" value="{{ old('start_time') }}"
						   id="start_time" class="start_time form-control"
						   placeholder="Arrival Time" required
					>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="end_time">Leaving Time</label>
					<input type="time" name="end_time" value="{{ old('end_time') }}"
						   id="end_time" class="end_time form-control"
						   placeholder="Leaving Time" required
					>
				</div>

				<div class="form-group pay_rate col-12 col-md-4">
					<label>Pay Rate (Input rate in one of following)</label>
					<div class="input-group">
						<input type="number" name="hourly_rate" value="{{ old('hourly_rate') }}"
							   id="hourly_rate" class="hourly_rate form-control"
							   placeholder="Hourly Rate" min="1" step="1" @if(empty(old('hourly_rate')) && empty(old('daily_rate')) && empty(old('weekly_rate'))) required @endif
						>
						<div class="input-group-append">
							<span class="input-group-text">Per Hour</span>
						</div>
					</div>
				</div>

				<div class="form-group pay_rate col-12 col-md-4">
					<label>&nbsp;</label>

					<div class="input-group">
						<input type="number" name="daily_rate" value="{{ old('daily_rate') }}"
							   id="daily_rate" class="daily_rate form-control"
							   placeholder="Daily Rate" min="1" step="1" @if(empty(old('hourly_rate')) && empty(old('daily_rate')) && empty(old('weekly_rate'))) required @endif
						>
						<div class="input-group-append">
							<span class="input-group-text">Per Day</span>
						</div>
					</div>
				</div>

				<div class="form-group pay_rate col-12 col-md-4">
					<label>&nbsp;</label>

					<div class="input-group">
						<input type="number" name="weekly_rate" value="{{ old('weekly_rate') }}"
							   id="weekly_rate" class="weekly_rate form-control"
							   placeholder="Weekly Rate" min="1" step="1" @if(empty(old('hourly_rate')) && empty(old('daily_rate')) && empty(old('weekly_rate'))) required @endif
						>
						<div class="input-group-append">
							<span class="input-group-text">Per Week</span>
						</div>
					</div>
				</div>

				{{--<div class="col-8 col-md-4 input-group mb-3 ml-auto">
					<input type="number" name="availability[1][charges]" class="form-control charges" placeholder=" Charges" value="20">
					<div class="input-group-append">
						<span class="input-group-text">Per Hour</span>
					</div>
				</div>--}}

				<div class="form-group col-12">
					<label for="desc">Description</label>
					<textarea type="text" name="desc"
						   id="title" class="desc form-control"
						   placeholder="Description" min="4" required
					>{{ old('desc') }}</textarea>
				</div>

				@if($role->id == 4)
					<div class="col-12 col-md-6">
						<div class="row">
							<div class="form-group col-12">
								<label>Required to Drive?</label>
								@foreach([ 'no', 'yes'] as $val)
									<label for="drive_{{ $val }}" class="float-right text-right" style="width: 80px;">
										<input type="radio" name="drive" value="{{ $val }}"
											   id="drive_{{ $val }}" class="drive custom-radio"
											   @if( $val == old('drive') ) checked @endif
											   required
										/> {{ ucfirst($val) }}
									</label>
								@endforeach
							</div>
						</div>
					</div>
					<div class="col-12 col-md-6"></div>
				@endif

				@include('app.pages.contract.create.service-type')

				@if($role->id == 5)
					<div class="form-group col-12 col-md-6">
						<label for="how_often">How often do you need cleaning?</label>
						<select name="how_often"
								id="how_often" class="how_often custom-select"
								required
						>
							@php $opts = ['Daily', 'Twice a week', 'Weekly', 'Every other week', 'Once a month', 'One time clean', 'Other']; @endphp
							@foreach($opts as $val)
								<option value="{{ $val }}"
										@if($val == old('how_often')) selected @endif
								>{{ $val }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group col-12 col-md-6">
						<label for="beds">How many bedroom(s) need cleaning?</label>
						<select name="beds"
								id="beds" class="beds custom-select"
								required
						>
							@php $opts = ['0 bedrooms', '1 bedroom', '2 bedrooms', '3 bedrooms', '4 bedrooms', '5+ bedrooms', 'Studio']; @endphp
							@foreach($opts as $val)
								<option value="{{ $val }}"
										@if($val == old('beds')) selected @endif
								>{{ $val }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group col-12 col-md-6">
						<label for="baths">How many bathroom(s) need cleaning?</label>
						<select name="baths"
								id="baths" class="baths custom-select"
								required
						>
							@php $opts = ['1 bathroom', '1 bathroom + 1 additional toilet', '2 bathrooms', '2 bathrooms + 1 additional toilet', '3 bathrooms', '4+ bathrooms']; @endphp
							@foreach($opts as $val)
								<option value="{{ $val }}"
										@if($val == old('baths')) selected @endif
								>{{ $val }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group col-12 col-md-6">
						<label for="rooms">How many reception room(s) need cleaning?</label>
						<select name="rooms"
								id="rooms" class="rooms custom-select"
								required
						>
							@php $opts = [ '0', '1', '2', '3', '4+']; @endphp
							@foreach($opts as $val)
								<option value="{{ $val }}"
										@if($val == old('rooms')) selected @endif
								>{{ $val }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-12 col-md-6">
						<label for="cleaning_type">What type of cleaning would you like?</label>
						<select name="cleaning_type"
								id="cleaning_type" class="cleaning_type custom-select"
								required
						>
							@php $opts = ['Standard cleaning', 'Deep cleaning', 'Move-out cleaning']; @endphp
							@foreach($opts as $val)
								<option value="{{ $val }}"
										@if($val == old('cleaning_type')) selected @endif
								>{{ $val }}</option>
							@endforeach
						</select>
					</div>
				@endif
				@include('app.pages.contract.create.working-days')
				@include('app.pages.contract.create.working-time')
				@include('app.pages.contract.create.languages')
				@include('app.pages.contract.create.experiences')
				@include('app.pages.contract.create.interests')

				<div class="form-group col-12">
					<button class="btn btn-primary float-right" type="submit">Submit <span class="fa fa-long-arrow-right ml-2"></span></button>
				</div>

			</form>
		</div>
	</div>
@endsection
