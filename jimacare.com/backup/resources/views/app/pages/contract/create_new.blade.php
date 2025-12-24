@extends('app.template.layout')

@section('content')
	<div class="row">
		<div class="mb-3 col-12 text-center">
			<h1>Still Need Support?</h1>
			<p>Please fill following form which only takes a minute or two.</p>
			<p>If you don't want to complete the form. You can <a href="tel:02038850856">give us a call</a> or <a href="mail:support@jimacare.com">drop me an
					email here.</a></p>
		</div>
	</div>
	<div class="requirements-form my-5">
		<div class="container">
			<div class="step-wrap">
				<div class="wizard">
					<div class="wizard-inner">
						<div class="connecting-line"></div>
						<ul class="nav nav-tabs" role="tablist">
							<li @if(!request('tab') || request('tab')=='choose-service') class="active" @endif>
								<a href="{{route('contract.create')}}?tab=choose-service"><span class="round-tab">1 </span> <i>Choose Service</i></a>
							</li>
							@guest()
								<li @if(request('tab')=='account') class="active" @endif>
									<a href="{{route('contract.create',['type'=>$type??''])}}?tab=account"><span class="round-tab">2 </span> <i>Account</i></a>
								</li>
							@else
								<li @if(request('tab')=='requirements') class="active" @endif>
									<a href="{{route('contract.create',['type'=>$type??''])}}?tab=requirements"><span class="round-tab">2 </span>
										<i>{{ucfirst($type)??'Service'}} Requirements</i></a>
								</li>
							@endif
							<li @if(request('tab')=='schedule') class="active" @endif>
								<a href="{{route('contract.create',['type'=>$type??''])}}?tab=schedule"><span class="round-tab">3 </span> <i>Time
										Requirements</i></a>
							</li>
							<li @if(request('tab')=='rate') class="active" @endif>
								<a href="{{route('contract.create',['type'=>$type??''])}}?tab=rate"><span class="round-tab">4 </span> <i>Rate Settlement</i></a>
							</li>
							<li @if(request('tab')=='other') class="active" @endif>
								<a href="{{route('contract.create',['type'=>$type??''])}}?tab=other"><span class="round-tab">5 </span> <i>Other Requirements</i></a>
							</li>
						</ul>
					</div>

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

					@if(!request('tab') || request('tab')=='choose-service')

						<div class="row">
							<div class="col-12 text-center">
								<h3>Who do you want to hire?</h3>
								<a class="btn btn-primary mx-3"
								   href="{{route('contract.create',['type'=>'carer'])}}?tab={{auth()->id()?'requirements':'account'}}">Carer</a>
								<a class="btn btn-primary mx-3"
								   href="{{route('contract.create',['type'=>'childminder'])}}?tab={{auth()->id()?'requirements':'account'}}">Childminder</a>
								<a class="btn btn-primary mx-3"
								   href="{{route('contract.create',['type'=>'housekeeper'])}}?tab={{auth()->id()?'requirements':'account'}}">Housekeeper</a>
							</div>
						</div>

					@elseif(auth()->guest() && request('tab')=='account')
						<h4 class="w-100 my-3">Personal Information</h4>
						<form class="row" method="POST" action="{{ route('contract.create', ['type' => $type??'']) }}?tab=account">
							@csrf

							<div class="form-group col-md-6">
								<label for="firstname">{{ __('First Name') }}</label>
								<input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname"
									   value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>
							</div>
							<div class="form-group col-md-6">
								<label for="lastname">{{ __('Last Name') }}</label>
								<input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname"
									   value="{{ old('lastname') }}" required>
							</div>

							<div class="form-group col-md-6">
								<label for="email">{{ __('E-Mail Address') }}</label>
								<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"
									   required autocomplete="email">
							</div>

							<div class="form-group col-md-6">
								<label for="phone">{{ __('Phone') }}</label>
								<input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}"
									   required autocomplete="phone">
							</div>

							<div class="form-group col-md-6">
								<label for="password">{{ __('Password') }}</label>
								<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required
									   autocomplete="new-password">
							</div>

							<div class="form-group col-md-6">
								<label for="password-confirm">{{ __('Confirm Password') }}</label>
								<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
									   autocomplete="new-password">
							</div>

							<div class="w-100 mt-3">
								<ul class="list-inline d-flex float-right">
									<li><a class="btn btn-dark mx-2" href="{{route('contract.create',['type'=> $type])."?tab=choose-service"}}">Back</a></li>
									<li>
										<button type="submit" class="btn btn-primary mx-2">Next</button>
									</li>
								</ul>
							</div>
						</form>

					@elseif(auth()->id() && request('tab')=='requirements')
						<h4 class="w-100 my-3">{{ucfirst($type)}} Requirement Details</h4>
						<form class="row" method="POST" action="{{ route('contract.create', ['type' => $type]) }}?tab=requirements">
							@csrf
							@if($contract)
								<input type="hidden" name="contract" value="{{$contract->id}}">
							@endif
							<div class="form-group col-md-4">
								<label for="title">Job Title</label>
								<input type="text" name="title" value="{{ old('title',$contract?$contract->title:'') }}"
									   id="title" class="title form-control"
									   placeholder="Title" min="4" required autofocus/>
							</div>

							<div class="form-group col-md-4">
								<label for="company">Company Name (If any)</label>
								<input type="text" name="company" value="{{ old('company',$contract?$contract->company:'') }}"
									   id="title" class="title form-control"
									   placeholder="Company Name (If any)" min="4"/>
							</div>

							<div class="form-group col-md-4">
								<label for="gender">{{ucfirst($type)}} Gender</label>
								<select class="title form-control" name="gender" required>
									<option value="nopreferences" @if(old('gender',$contract?$contract->gender:'')=='nopreferences') selected @endif>No
										preferences
									</option>
									<option value="male" @if(old('gender',$contract?$contract->gender:'')=='male') selected @endif>Male</option>
									<option value="female" @if(old('gender',$contract?$contract->gender:'')=='female') selected @endif>Female</option>
								</select>
							</div>
							@if($contract)
								@include('app.pages.contract.edit.experiences')
							@else
								@include('app.pages.contract.create.experiences')
							@endif

							<div class="w-100 mt-3">
								<ul class="list-inline d-flex float-right">
									<li><a class="btn btn-dark mx-2" href="{{route('contract.create',['type'=> $type])."?tab=choose-service"}}">Back</a></li>
									<li>
										<button type="submit" class="btn btn-primary mx-2">Next</button>
									</li>
								</ul>
							</div>
						</form>

					@elseif(auth()->id() && request('tab')=='schedule')
						<h4 class="w-100 my-3">{{ucfirst($type)}} Time Requirements</h4>
						<form class="row" method="POST" action="{{ route('contract.create', ['type' => $type]) }}?tab=schedule">
							@csrf
							<input type="hidden" name="contract" value="{{$contract->id}}">

							<div class="form-group col-md-6">
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
												@if($key == $contract->start_type) selected @endif
										>{{ $val }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group col-md-6">
								<label for="start_date">Start Date</label>
								<input type="date" name="start_date" value="{{ $contract->start_date?$contract->start_date->format('Y-m-d'):'' }}"
									   id="period_date" class="period_date form-control"
									   placeholder="Period End Date" required
								>
							</div>

							<div class="form-group col-md-6">
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
												@if($key == $contract->end_type) selected @endif
										>{{ $val }}</option>
									@endforeach
								</select>
							</div>

							<div class="form-group col-md-6">
								<label for="end_date">End Date</label>
								<input type="date" name="end_date" value="{{ $contract->end_date?$contract->end_date->format('Y-m-d'):'' }}"
									   id="end_date" class="end_date form-control"
									   placeholder="End Date" required
								>
							</div>

							<div class="form-group col-md-6">
								<label for="start_time">Arrival Time</label>
								<input type="time" name="start_time" value="{{ $contract->start_time?$contract->start_time->format('H:i'):'' }}"
									   id="start_time" class="start_time form-control"
									   placeholder="Arrival Time" required
								>
							</div>

							<div class="form-group col-md-6">
								<label for="end_time">Leaving Time</label>
								<input type="time" name="end_time" value="{{ $contract->end_time?$contract->end_time->format('H:i'):'' }}"
									   id="end_time" class="end_time form-control"
									   placeholder="Leaving Time" required
								>
							</div>

							<div class="w-100 mt-3">
								<ul class="list-inline d-flex float-right">
									<li><a class="btn btn-dark mx-2" href="{{route('contract.create',['type'=> $type])."?tab=requirements"}}">Back</a></li>
									<li>
										<button type="submit" class="btn btn-primary mx-2">Next</button>
									</li>
								</ul>
							</div>
						</form>

					@elseif(auth()->id() && request('tab')=='rate')
						<h4 class="w-100 my-3">{{ucfirst($type)}} Rate Settlement</h4>
						<form class="row" method="POST" action="{{ route('contract.create', ['type' => $type]) }}?tab=rate">
							@csrf
							<input type="hidden" name="contract" value="{{$contract->id}}">
							<div class="form-group pay_rate col-12 col-md-4">
								<label>Pay Rate</label>

								<div class="input-group">
									<input type="number" name="hourly_rate" value="{{ $contract->hourly_rate }}"
										   id="hourly_rate" class="hourly_rate form-control"
										   placeholder="Hourly Rate" min="1" step="1" required
									>
									<div class="input-group-append">
										<span class="input-group-text">Per Hour</span>
									</div>
								</div>
							</div>

							<div class="form-group pay_rate col-12 col-md-4">
								<label>&nbsp;</label>

								<div class="input-group">
									<input type="number" name="daily_rate" value="{{ $contract->daily_rate }}"
										   id="daily_rate" class="daily_rate form-control"
										   placeholder="Daily Rate" min="1" step="1" required
									>
									<div class="input-group-append">
										<span class="input-group-text">Per Day</span>
									</div>
								</div>
							</div>

							<div class="form-group pay_rate col-12 col-md-4">
								<label>&nbsp;</label>

								<div class="input-group">
									<input type="number" name="weekly_rate" value="{{ $contract->weekly_rate }}"
										   id="weekly_rate" class="weekly_rate form-control"
										   placeholder="Weekly Rate" min="1" step="1" required
									>
									<div class="input-group-append">
										<span class="input-group-text">Per Week</span>
									</div>
								</div>
							</div>

							<div class="form-group col-12">
								<label for="desc">Is there any extra information you want to share?</label>
								<textarea type="text" name="desc"
										  id="title" class="desc form-control"
										  placeholder="Description" min="4" required
								>{{ $contract->desc }}</textarea>
							</div>

							<div class="w-100 mt-3">
								<ul class="list-inline d-flex float-right">
									<li><a class="btn btn-dark mx-2" href="{{route('contract.create',['type'=> $type])."?tab=schedule"}}">Back</a></li>
									<li>
										<button type="submit" class="btn btn-primary mx-2">Next</button>
									</li>
								</ul>
							</div>
						</form>

					@elseif(auth()->id() && request('tab')=='other')
						<h4 class="w-100 my-3">{{ucfirst($type)}} Other Requirements</h4>
						<form class="row" method="POST" action="{{ route('contract.create', ['type' => $type]) }}?tab=other">
							@csrf
							<input type="hidden" name="contract" value="{{$contract->id}}">

							@if($role->id == 4)
								<div class="col-12 col-md-6">
									<div class="row">
										<div class="form-group col-12">
											<label>Required to Drive?</label>
											@foreach([ 0 => 'no', 1 => 'yes'] as $key => $val)
												<label for="drive_{{ $val }}" class="float-right text-right" style="width: 80px;">
													<input type="radio" name="drive" value="{{ $val }}"
														   id="drive_{{ $val }}" class="drive custom-radio"
														   @if( $key == $contract->drive) checked @endif
														   required
													/> {{ ucfirst($val) }}
												</label>
											@endforeach
										</div>
									</div>
								</div>
								<div class="col-12 col-md-6"></div>
							@endif

							@include('app.pages.contract.edit.service-type')

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
													@if($val == $contract->how_often) selected @endif
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
													@if($val == $contract->beds) selected @endif
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
													@if($val == $contract->baths) selected @endif
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
													@if($val == $contract->rooms) selected @endif
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
													@if($val == $contract->cleaning_type) selected @endif
											>{{ $val }}</option>
										@endforeach
									</select>
								</div>
							@endif
							@include('app.pages.contract.edit.working-days')
							@include('app.pages.contract.edit.working-time')
							@include('app.pages.contract.edit.languages')
							@include('app.pages.contract.edit.interests')

							<div class="w-100 mt-3">
								<ul class="list-inline d-flex float-right">
									<li><a class="btn btn-dark mx-2" href="{{route('contract.create',['type'=> $type])."?tab=rate"}}">Back</a></li>
									<li>
										<button type="submit" class="btn btn-primary mx-2">Finish</button>
									</li>
								</ul>
							</div>
						</form>
					@endif

				</div>
			</div>
		</div>
	</div>
@endsection
