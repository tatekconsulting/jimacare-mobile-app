@extends('app.template.auth-layout')

@section('content')
	<div class="login-page pb-5">
		<div class="container">
			<div class="row">
				<div class="col-12 col-md-7">
					@if(session()->has('notice'))
						<div class='alert alert-{{ session('type') }} alert-notice alert-dismissible fade show'
							 role='alert'>
							{{ session('notice') }}
							<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
								<span aria-hidden='true'>&times;</span>
							</button>
						</div>
					@endif
					<h4>{{ __('Verify Your Phone Number') }}</h4>
					<p>Please enter the OTP sent to your number: {{auth()->user()->phone??''}}</p>
					<form action="{{route('verify.phone.otp')}}" method="post">
						@csrf
						<div class="form-group row">
							<label for="verification_code"
								   class="col-md-4 col-form-label text-md-right">{{ __('OTP CODE') }}</label>
							<div class="col-md-6">
								<input id="verification_code" type="number"
									   class="form-control @error('verification_code') is-invalid @enderror"
									   name="verification_code" value="{{ old('verification_code') }}" required>
								@error('verification_code')
								<span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
								@enderror
							</div>
						</div>
						<div class="form-group row">
							<div class="col-10 text-right">
								<button type="submit" class="btn btn-primary align-baseline">
									{{ __('Verify') }}
								</button>
							</div>
						</div>
						<div class="form-group row mb-0">
							<div class="col-10 text-right">
								<a href="{{route('resend.phone.otp')}}" class="btn btn-link p-0 m-0 align-baseline">
									{{ __('Resend OTP') }}
								</a>
							</div>
						</div>
					</form>
				</div>
				<div class="col-12 col-md-5">
					<div class="login-img">
						<img src="{{ asset('img/login-image.png') }}" alt="">
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
