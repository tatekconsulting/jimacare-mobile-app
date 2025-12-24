@extends('app.template.layout')

@section('content')
<style>
	/* Modern Post Job Page Styles */
	.post-job-container {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		min-height: 100vh;
		padding: 40px 0;
	}

	.post-job-wrapper {
		max-width: 1200px;
		margin: 0 auto;
		background: #fff;
		border-radius: 20px;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
		overflow: hidden;
	}

	/* Modern Step Wizard */
	.modern-wizard {
		background: #f8f9fa;
		padding: 30px;
		border-bottom: 2px solid #e9ecef;
	}

	.wizard-steps {
		display: flex;
		justify-content: space-between;
		align-items: center;
		position: relative;
		margin-bottom: 20px;
	}

	.wizard-steps::before {
		content: '';
		position: absolute;
		top: 20px;
		left: 0;
		right: 0;
		height: 3px;
		background: #e9ecef;
		z-index: 1;
	}

	.step-item {
		flex: 1;
		text-align: center;
		position: relative;
		z-index: 2;
	}

	.step-circle {
		width: 45px;
		height: 45px;
		border-radius: 50%;
		background: #fff;
		border: 3px solid #e9ecef;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 10px;
		font-weight: 700;
		font-size: 18px;
		color: #6c757d;
		transition: all 0.3s ease;
		box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
	}

	.step-item.active .step-circle {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		border-color: #667eea;
		color: #fff;
		transform: scale(1.1);
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
	}

	.step-item.completed .step-circle {
		background: #28a745;
		border-color: #28a745;
		color: #fff;
	}

	.step-item.completed .step-circle::after {
		content: '✓';
		font-size: 20px;
	}

	.step-label {
		font-size: 13px;
		color: #6c757d;
		font-weight: 500;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.step-item.active .step-label {
		color: #667eea;
		font-weight: 700;
	}

	/* Modern Form Container */
	.form-container {
		padding: 50px;
	}

	.form-section-title {
		font-size: 28px;
		font-weight: 700;
		color: #2c3e50;
		margin-bottom: 10px;
		display: flex;
		align-items: center;
		gap: 15px;
	}

	.form-section-title::before {
		content: '';
		width: 4px;
		height: 35px;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		border-radius: 2px;
	}

	.form-section-subtitle {
		color: #6c757d;
		margin-bottom: 30px;
		font-size: 16px;
	}

	/* Modern Form Groups */
	.modern-form-group {
		margin-bottom: 25px;
	}

	.modern-label {
		font-weight: 600;
		color: #2c3e50;
		margin-bottom: 8px;
		display: block;
		font-size: 14px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.modern-input,
	.modern-select,
	.modern-textarea {
		width: 100%;
		padding: 14px 18px;
		border: 2px solid #e9ecef;
		border-radius: 10px;
		font-size: 15px;
		transition: all 0.3s ease;
		background: #fff;
	}

	.modern-input:focus,
	.modern-select:focus,
	.modern-textarea:focus {
		outline: none;
		border-color: #667eea;
		box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
		transform: translateY(-2px);
	}

	.modern-textarea {
		min-height: 120px;
		resize: vertical;
	}

	/* Service Selection Cards */
	.service-cards {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 20px;
		margin: 30px 0;
	}

	.service-card {
		background: #fff;
		border: 3px solid #e9ecef;
		border-radius: 15px;
		padding: 30px;
		text-align: center;
		cursor: pointer;
		transition: all 0.3s ease;
		text-decoration: none;
		color: inherit;
		display: block;
	}

	.service-card:hover {
		border-color: #667eea;
		transform: translateY(-5px);
		box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
	}

	.service-card-icon {
		font-size: 48px;
		margin-bottom: 15px;
	}

	.service-card-title {
		font-size: 20px;
		font-weight: 700;
		color: #2c3e50;
		margin-bottom: 10px;
	}

	.service-card-desc {
		color: #6c757d;
		font-size: 14px;
	}

	/* Modern Buttons */
	.modern-btn {
		padding: 14px 35px;
		border-radius: 10px;
		font-weight: 600;
		font-size: 15px;
		border: none;
		cursor: pointer;
		transition: all 0.3s ease;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		display: inline-flex;
		align-items: center;
		gap: 10px;
	}

	.modern-btn-primary {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: #fff;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
	}

	.modern-btn-primary:hover {
		transform: translateY(-2px);
		box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
	}

	.modern-btn-secondary {
		background: #6c757d;
		color: #fff;
	}

	.modern-btn-secondary:hover {
		background: #5a6268;
		transform: translateY(-2px);
	}

	/* Form Actions */
	.form-actions {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-top: 40px;
		padding-top: 30px;
		border-top: 2px solid #e9ecef;
	}

	/* Experience Selection */
	.experience-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
		gap: 15px;
		margin-top: 15px;
	}

	.experience-card {
		background: #f8f9fa;
		border: 2px solid #e9ecef;
		border-radius: 10px;
		padding: 15px;
		cursor: pointer;
		transition: all 0.3s ease;
		text-align: center;
	}

	.experience-card:hover {
		border-color: #667eea;
		background: #fff;
		transform: translateY(-3px);
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
	}

	.experience-card input[type="checkbox"] {
		display: none;
	}

	.experience-card input[type="checkbox"]:checked + label {
		color: #667eea;
		font-weight: 700;
	}

	.experience-card input[type="checkbox"]:checked ~ .experience-card {
		border-color: #667eea;
		background: #f0f4ff;
	}

	/* Alert Styles */
	.modern-alert {
		padding: 18px 25px;
		border-radius: 10px;
		margin-bottom: 25px;
		border-left: 4px solid;
	}

	.modern-alert-danger {
		background: #fff5f5;
		border-color: #fc8181;
		color: #c53030;
	}

	/* Radio Button Styles */
	.modern-radio-group {
		display: flex;
		gap: 20px;
		margin-top: 10px;
	}

	.modern-radio {
		flex: 1;
		position: relative;
	}

	.modern-radio input[type="radio"] {
		display: none;
	}

	.modern-radio label {
		display: block;
		padding: 15px 20px;
		background: #f8f9fa;
		border: 2px solid #e9ecef;
		border-radius: 10px;
		text-align: center;
		cursor: pointer;
		transition: all 0.3s ease;
		font-weight: 600;
		color: #6c757d;
	}

	.modern-radio input[type="radio"]:checked + label {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		border-color: #667eea;
		color: #fff;
		box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
	}

	/* Responsive */
	@media (max-width: 768px) {
		.form-container {
			padding: 30px 20px;
		}

		.wizard-steps {
			flex-direction: column;
			gap: 20px;
		}

		.wizard-steps::before {
			display: none;
		}

		.service-cards {
			grid-template-columns: 1fr;
		}

		.form-actions {
			flex-direction: column;
			gap: 15px;
		}

		.modern-btn {
			width: 100%;
			justify-content: center;
		}
	}

	/* Animation */
	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(20px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.form-container > * {
		animation: fadeIn 0.5s ease;
	}
</style>

<div class="post-job-container">
	<div class="container">
		<div class="post-job-wrapper">
			<!-- Modern Step Wizard -->
			<div class="modern-wizard">
				<div class="wizard-steps">
					<div class="step-item {{ !request('tab') || request('tab')=='choose-service' ? 'active' : 'completed' }}">
						<div class="step-circle">1</div>
						<div class="step-label">Choose Service</div>
					</div>
					@guest()
						<div class="step-item {{ request('tab')=='account' ? 'active' : (request('tab') && request('tab')!='choose-service' ? 'completed' : '') }}">
							<div class="step-circle">2</div>
							<div class="step-label">Account</div>
						</div>
					@else
						<div class="step-item {{ request('tab')=='requirements' ? 'active' : (request('tab') && request('tab')!='choose-service' && request('tab')!='requirements' ? 'completed' : '') }}">
							<div class="step-circle">2</div>
							<div class="step-label">Requirements</div>
						</div>
					@endif
					<div class="step-item {{ request('tab')=='schedule' ? 'active' : (request('tab')=='rate' || request('tab')=='other' ? 'completed' : '') }}">
						<div class="step-circle">3</div>
						<div class="step-label">Time</div>
					</div>
					<div class="step-item {{ request('tab')=='rate' ? 'active' : (request('tab')=='other' ? 'completed' : '') }}">
						<div class="step-circle">4</div>
						<div class="step-label">Rate</div>
					</div>
					<div class="step-item {{ request('tab')=='other' ? 'active' : '' }}">
						<div class="step-circle">5</div>
						<div class="step-label">Other</div>
					</div>
				</div>
			</div>

			<!-- Form Container -->
			<div class="form-container">
				@if ($errors->any())
					<div class="modern-alert modern-alert-danger">
						<strong>Please fix the following errors:</strong>
						<ul class="mb-0 mt-2">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				@if(!request('tab') || request('tab')=='choose-service')
					<div class="text-center">
						<h2 class="form-section-title" style="justify-content: center;">
							<span>Who do you want to hire?</span>
						</h2>
						<p class="form-section-subtitle">Select the type of service provider you need</p>
						
						<div class="service-cards">
							<a href="{{route('contract.create',['type'=>'carer'])}}?tab={{auth()->id()?'requirements':'account'}}" class="service-card">
								<div class="service-card-icon">👨‍⚕️</div>
								<div class="service-card-title">Carer</div>
								<div class="service-card-desc">Professional care services for your loved ones</div>
							</a>
							<a href="{{route('contract.create',['type'=>'childminder'])}}?tab={{auth()->id()?'requirements':'account'}}" class="service-card">
								<div class="service-card-icon">👶</div>
								<div class="service-card-title">Childminder</div>
								<div class="service-card-desc">Expert childcare and supervision services</div>
							</a>
							<a href="{{route('contract.create',['type'=>'housekeeper'])}}?tab={{auth()->id()?'requirements':'account'}}" class="service-card">
								<div class="service-card-icon">🏠</div>
								<div class="service-card-title">Housekeeper</div>
								<div class="service-card-desc">Complete housekeeping and cleaning services</div>
							</a>
						</div>
					</div>

				@elseif(auth()->guest() && request('tab')=='account')
					<h2 class="form-section-title">Create Your Account</h2>
					<p class="form-section-subtitle">Please provide your personal information to continue</p>
					
					<form method="POST" action="{{ route('contract.create', ['type' => $type??'']) }}?tab=account">
						@csrf
						<div class="row">
							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="firstname">{{ __('First Name') }}</label>
								<input id="firstname" type="text" class="modern-input @error('firstname') is-invalid @enderror" name="firstname"
									   value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>
							</div>
							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="lastname">{{ __('Last Name') }}</label>
								<input id="lastname" type="text" class="modern-input @error('lastname') is-invalid @enderror" name="lastname"
									   value="{{ old('lastname') }}" required>
							</div>
							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="email">{{ __('E-Mail Address') }}</label>
								<input id="email" type="email" class="modern-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"
									   required autocomplete="email">
							</div>
							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="phone">{{ __('Phone') }}</label>
								<input id="phone" type="tel" class="modern-input @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}"
									   required autocomplete="phone">
							</div>
							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="password">{{ __('Password') }}</label>
								<input id="password" type="password" class="modern-input @error('password') is-invalid @enderror" name="password" required
									   autocomplete="new-password">
							</div>
							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="password-confirm">{{ __('Confirm Password') }}</label>
								<input id="password-confirm" type="password" class="modern-input" name="password_confirmation" required
									   autocomplete="new-password">
							</div>
						</div>
						<div class="form-actions">
							<a href="{{route('contract.create',['type'=> $type])."?tab=choose-service"}}" class="modern-btn modern-btn-secondary">
								← Back
							</a>
							<button type="submit" class="modern-btn modern-btn-primary">
								Next →
							</button>
						</div>
					</form>

				@elseif(auth()->id() && request('tab')=='requirements')
					<h2 class="form-section-title">{{ucfirst($type)}} Requirement Details</h2>
					<p class="form-section-subtitle">Tell us about the job requirements and preferences</p>
					
					<form method="POST" action="{{ route('contract.create', ['type' => $type]) }}?tab=requirements">
						@csrf
						@if($contract)
							<input type="hidden" name="contract" value="{{$contract->id}}">
						@endif
						
						<div class="row">
							<div class="col-md-4 modern-form-group">
								<label class="modern-label" for="title">Job Title *</label>
								<input type="text" name="title" value="{{ old('title',$contract?$contract->title:'') }}"
									   id="title" class="modern-input"
									   placeholder="e.g., Live-in Carer for Elderly" required autofocus/>
							</div>

							<div class="col-md-4 modern-form-group">
								<label class="modern-label" for="company">Company Name (Optional)</label>
								<input type="text" name="company" value="{{ old('company',$contract?$contract->company:'') }}"
									   id="company" class="modern-input"
									   placeholder="Company Name (If any)"/>
							</div>

							<div class="col-md-4 modern-form-group">
								<label class="modern-label" for="gender">{{ucfirst($type)}} Gender Preference *</label>
								<select class="modern-select" name="gender" required>
									<option value="nopreferences" @if(old('gender',$contract?$contract->gender:'')=='nopreferences') selected @endif>No preferences</option>
									<option value="male" @if(old('gender',$contract?$contract->gender:'')=='male') selected @endif>Male</option>
									<option value="female" @if(old('gender',$contract?$contract->gender:'')=='female') selected @endif>Female</option>
								</select>
							</div>
						</div>

						<div class="modern-form-group">
							<label class="modern-label">Required Experience *</label>
							@if($contract)
								@include('app.pages.contract.edit.experiences')
							@else
								@include('app.pages.contract.create.experiences')
							@endif
						</div>

						<div class="form-actions">
							<a href="{{route('contract.create',['type'=> $type])."?tab=choose-service"}}" class="modern-btn modern-btn-secondary">
								← Back
							</a>
							<button type="submit" class="modern-btn modern-btn-primary">
								Next →
							</button>
						</div>
					</form>

				@elseif(auth()->id() && request('tab')=='schedule')
					<h2 class="form-section-title">{{ucfirst($type)}} Time Requirements</h2>
					<p class="form-section-subtitle">Specify when and how long you need the service</p>
					
					<form method="POST" action="{{ route('contract.create', ['type' => $type]) }}?tab=schedule">
						@csrf
						<input type="hidden" name="contract" value="{{$contract->id}}">

						<div class="row">
							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="start_type">When would you like to start? *</label>
								<select name="start_type" id="start_type" class="modern-select" required>
									@php $starts = [
										'immediately' => 'Immediately',
										'not-sure'  => 'Not Sure',
										'specific-date' => 'Specific Date',
									]; @endphp
									@foreach($starts as $key => $val)
										<option value="{{ $key }}" @if($key == $contract->start_type) selected @endif>{{ $val }}</option>
									@endforeach
								</select>
							</div>

							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="start_date">Start Date *</label>
								<input type="date" name="start_date" value="{{ $contract->start_date?$contract->start_date->format('Y-m-d'):'' }}"
									   id="start_date" class="modern-input" required>
							</div>

							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="end_type">When would you like to end? *</label>
								<select name="end_type" id="end_type" class="modern-select" required>
									@php $ends = [
										'fixed-period' => 'Fixed Period',
										'on-going' => 'On Going'
									]; @endphp
									@foreach($ends as $key => $val)
										<option value="{{ $key }}" @if($key == $contract->end_type) selected @endif>{{ $val }}</option>
									@endforeach
								</select>
							</div>

							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="end_date">End Date *</label>
								<input type="date" name="end_date" value="{{ $contract->end_date?$contract->end_date->format('Y-m-d'):'' }}"
									   id="end_date" class="modern-input" required>
							</div>

							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="start_time">Arrival Time *</label>
								<input type="time" name="start_time" value="{{ $contract->start_time?$contract->start_time->format('H:i'):'' }}"
									   id="start_time" class="modern-input" required>
							</div>

							<div class="col-md-6 modern-form-group">
								<label class="modern-label" for="end_time">Leaving Time *</label>
								<input type="time" name="end_time" value="{{ $contract->end_time?$contract->end_time->format('H:i'):'' }}"
									   id="end_time" class="modern-input" required>
							</div>
						</div>

						<div class="form-actions">
							<a href="{{route('contract.create',['type'=> $type])."?tab=requirements"}}" class="modern-btn modern-btn-secondary">
								← Back
							</a>
							<button type="submit" class="modern-btn modern-btn-primary">
								Next →
							</button>
						</div>
					</form>

				@elseif(auth()->id() && request('tab')=='rate')
					<h2 class="form-section-title">{{ucfirst($type)}} Rate Settlement</h2>
					<p class="form-section-subtitle">Set the payment rate for this position</p>
					
					<form method="POST" action="{{ route('contract.create', ['type' => $type]) }}?tab=rate">
						@csrf
						<input type="hidden" name="contract" value="{{$contract->id}}">
						
						<div class="row">
							<div class="col-md-4 modern-form-group">
								<label class="modern-label">Hourly Rate *</label>
								<div class="input-group">
									<input type="number" name="hourly_rate" value="{{ $contract->hourly_rate }}"
										   class="modern-input" placeholder="0.00" min="1" step="0.01" required>
									<div class="input-group-append">
										<span class="input-group-text" style="border-left: none; border-radius: 0 10px 10px 0; background: #f8f9fa; border: 2px solid #e9ecef; border-left: none;">£/Hour</span>
									</div>
								</div>
							</div>

							<div class="col-md-4 modern-form-group">
								<label class="modern-label">Daily Rate *</label>
								<div class="input-group">
									<input type="number" name="daily_rate" value="{{ $contract->daily_rate }}"
										   class="modern-input" placeholder="0.00" min="1" step="0.01" required>
									<div class="input-group-append">
										<span class="input-group-text" style="border-left: none; border-radius: 0 10px 10px 0; background: #f8f9fa; border: 2px solid #e9ecef; border-left: none;">£/Day</span>
									</div>
								</div>
							</div>

							<div class="col-md-4 modern-form-group">
								<label class="modern-label">Weekly Rate *</label>
								<div class="input-group">
									<input type="number" name="weekly_rate" value="{{ $contract->weekly_rate }}"
										   class="modern-input" placeholder="0.00" min="1" step="0.01" required>
									<div class="input-group-append">
										<span class="input-group-text" style="border-left: none; border-radius: 0 10px 10px 0; background: #f8f9fa; border: 2px solid #e9ecef; border-left: none;">£/Week</span>
									</div>
								</div>
							</div>

							<div class="col-12 modern-form-group">
								<label class="modern-label" for="desc">Additional Information</label>
								<textarea name="desc" id="desc" class="modern-textarea"
										  placeholder="Is there any extra information you want to share?">{{ $contract->desc }}</textarea>
							</div>
						</div>

						<div class="form-actions">
							<a href="{{route('contract.create',['type'=> $type])."?tab=schedule"}}" class="modern-btn modern-btn-secondary">
								← Back
							</a>
							<button type="submit" class="modern-btn modern-btn-primary">
								Next →
							</button>
						</div>
					</form>

				@elseif(auth()->id() && request('tab')=='other')
					<h2 class="form-section-title">{{ucfirst($type)}} Other Requirements</h2>
					<p class="form-section-subtitle">Specify additional requirements and preferences</p>
					
					<form method="POST" action="{{ route('contract.create', ['type' => $type]) }}?tab=other">
						@csrf
						<input type="hidden" name="contract" value="{{$contract->id}}">

						@if($role->id == 4)
							<div class="modern-form-group">
								<label class="modern-label">Required to Drive? *</label>
								<div class="modern-radio-group">
									<div class="modern-radio">
										<input type="radio" name="drive" value="no" id="drive_no" @if($contract->drive == 0) checked @endif required>
										<label for="drive_no">No</label>
									</div>
									<div class="modern-radio">
										<input type="radio" name="drive" value="yes" id="drive_yes" @if($contract->drive == 1) checked @endif required>
										<label for="drive_yes">Yes</label>
									</div>
								</div>
							</div>
						@endif

						@include('app.pages.contract.edit.service-type')

						@if($role->id == 5)
							<div class="row">
								<div class="col-md-6 modern-form-group">
									<label class="modern-label" for="how_often">How often do you need cleaning? *</label>
									<select name="how_often" id="how_often" class="modern-select" required>
										@php $opts = ['Daily', 'Twice a week', 'Weekly', 'Every other week', 'Once a month', 'One time clean', 'Other']; @endphp
										@foreach($opts as $val)
											<option value="{{ $val }}" @if($val == $contract->how_often) selected @endif>{{ $val }}</option>
										@endforeach
									</select>
								</div>

								<div class="col-md-6 modern-form-group">
									<label class="modern-label" for="beds">How many bedroom(s) need cleaning? *</label>
									<select name="beds" id="beds" class="modern-select" required>
										@php $opts = ['0 bedrooms', '1 bedroom', '2 bedrooms', '3 bedrooms', '4 bedrooms', '5+ bedrooms', 'Studio']; @endphp
										@foreach($opts as $val)
											<option value="{{ $val }}" @if($val == $contract->beds) selected @endif>{{ $val }}</option>
										@endforeach
									</select>
								</div>

								<div class="col-md-6 modern-form-group">
									<label class="modern-label" for="baths">How many bathroom(s) need cleaning? *</label>
									<select name="baths" id="baths" class="modern-select" required>
										@php $opts = ['1 bathroom', '1 bathroom + 1 additional toilet', '2 bathrooms', '2 bathrooms + 1 additional toilet', '3 bathrooms', '4+ bathrooms']; @endphp
										@foreach($opts as $val)
											<option value="{{ $val }}" @if($val == $contract->baths) selected @endif>{{ $val }}</option>
										@endforeach
									</select>
								</div>

								<div class="col-md-6 modern-form-group">
									<label class="modern-label" for="rooms">How many reception room(s) need cleaning? *</label>
									<select name="rooms" id="rooms" class="modern-select" required>
										@php $opts = [ '0', '1', '2', '3', '4+']; @endphp
										@foreach($opts as $val)
											<option value="{{ $val }}" @if($val == $contract->rooms) selected @endif>{{ $val }}</option>
										@endforeach
									</select>
								</div>

								<div class="col-md-6 modern-form-group">
									<label class="modern-label" for="cleaning_type">What type of cleaning would you like? *</label>
									<select name="cleaning_type" id="cleaning_type" class="modern-select" required>
										@php $opts = ['Standard cleaning', 'Deep cleaning', 'Move-out cleaning']; @endphp
										@foreach($opts as $val)
											<option value="{{ $val }}" @if($val == $contract->cleaning_type) selected @endif>{{ $val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						@endif

						@include('app.pages.contract.edit.working-days')
						@include('app.pages.contract.edit.working-time')
						@include('app.pages.contract.edit.languages')
						@include('app.pages.contract.edit.interests')
						@include('app.pages.contract.edit.location')

						<div class="form-actions">
							<a href="{{route('contract.create',['type'=> $type])."?tab=rate"}}" class="modern-btn modern-btn-secondary">
								← Back
							</a>
							<button type="submit" class="modern-btn modern-btn-primary">
								✓ Finish
							</button>
						</div>
					</form>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
