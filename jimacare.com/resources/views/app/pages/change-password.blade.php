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
					<h4>{{ __('Change Your Password') }}</h4>
					<p>You are using a temporary password. Please create a new secure password to continue.</p>
					<form action="{{ route('password.change.submit') }}" method="post">
						@csrf
						<div class="form-group row">
							<label for="password"
								   class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>
							<div class="col-md-6">
								<input id="password" type="password"
									   class="form-control @error('password') is-invalid @enderror"
									   name="password" required autofocus minlength="8"
									   placeholder="Enter new password (min. 8 characters)">
								@error('password')
								<span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
								@enderror
								<small class="form-text text-muted">Password must be at least 8 characters long.</small>
							</div>
						</div>
						<div class="form-group row">
							<label for="password_confirmation"
								   class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>
							<div class="col-md-6">
								<input id="password_confirmation" type="password"
									   class="form-control @error('password_confirmation') is-invalid @enderror"
									   name="password_confirmation" required minlength="8"
									   placeholder="Confirm new password">
								@error('password_confirmation')
								<span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
								@enderror
							</div>
						</div>
						<div class="form-group row">
							<div class="col-10 text-right">
								<button type="submit" class="btn btn-primary align-baseline">
									{{ __('Change Password') }}
								</button>
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

