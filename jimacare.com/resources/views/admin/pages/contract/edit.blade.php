@extends('admin.template.layout')

@section('content')
	<div class="card mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">EDIT USER <a href="{{ route('dashboard.contract.index') }}" class="btn btn-primary btn-sm float-right">MANAGE ALL</a></h6>
		</div>
		<div class="card-body">
			<form class="row" action="{{ route('dashboard.contract.update', [ 'contract' => $contract->id]) }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="form-group col-12">
					<label for="title">Title</label>
					<input type="text" name="title" value="{{ $contract->title }}"
						   id="title" class="title form-control"
						   placeholder="Title" min="4" required autofocus
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
									@if($key == $contract->start_type) selected @endif
							>{{ $val }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="start_date">Start Date</label>
					<input type="date" name="start_date" value="{{ $contract->start_date ? $contract->start_date->format('Y-m-d') : '' }}"
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
									@if($key == $contract->end_type) selected @endif
							>{{ $val }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="end_date">End Date</label>
					<input type="date" name="end_date" value="{{ $contract->end_date ? $contract->end_date->format('Y-m-d') : '' }}"
						   id="end_date" class="end_date form-control"
						   placeholder="End Date" required
					>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="start_time">Arrival Time</label>
					<input type="time" name="start_time" value="{{ $contract->start_time ? $contract->start_time->format('H:i') : '' }}"
						   id="start_time" class="start_time form-control"
						   placeholder="Arrival Time" required
					>
				</div>

				<div class="form-group col-12 col-md-6">
					<label for="end_time">Leaving Time</label>
					<input type="time" name="end_time" value="{{ $contract->end_time ? $contract->end_time->format('H:i') : '' }}"
						   id="end_time" class="end_time form-control"
						   placeholder="Leaving Time" required
					>
				</div>

				<div class="form-group col-12">
					<label for="desc">Description</label>
					<textarea type="text" name="desc"
							  id="title" class="desc form-control"
							  placeholder="Description" min="4" required
					>{{ $contract->desc }}</textarea>
				</div>

				@if(($role->id ?? 0) == 4)
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

				@if(($role->id ?? 0) == 5)
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
				@include('app.pages.contract.edit.experiences')
				@include('app.pages.contract.edit.interests')


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
