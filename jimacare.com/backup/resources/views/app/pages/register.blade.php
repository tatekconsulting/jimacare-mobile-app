@extends('app.template.auth-layout')

@section('content')
	<div class="login-page pb-5">
		<div class="container">
			<div class="row">
				<div class="col-12 col-md-7">
					<h2>Sign up & <span>become a {{ $role->title  }}</span></h2>
					<form method="POST" action="{{ route('register') }}" class="row">
						@csrf

						<input type="hidden" name="role" value="{{ $role->id }}">
						<div class="form-group col-12 col-md-6">
							<label for="firstname">First Name</label>
							<input type="text" name="firstname" value="{{ old('firstname') }}"
								   id="firstname" class="form-control firstname @error('firstname') is-invalid @enderror"
								   placeholder="First Name" required autofocus
							/>
							@error('firstname')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group col-12 col-md-6">
							<label for="lastname">Last Name</label>
							<input type="text" name="lastname" value="{{ old('lastname') }}"
								   id="lastname" class="form-control lastname @error('lastname') is-invalid @enderror"
								   placeholder="Last Name" required
							/>
							@error('lastname')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group col-12 col-md-6">
							<label for="email">Email</label>
							<input type="email" name="email" value="{{ old('email') }}"
								   id="email" class="form-control email @error('email') is-invalid @enderror"
								   placeholder="Email Address" required
							/>
							@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group col-12 col-md-6">
							<label for="phone">Phone</label>
							<input type="tel" name="phone" value="{{ old('phone') }}"
								   id="phone" class="form-control phone @error('phone') is-invalid @enderror"
								   placeholder="Phone Number" required
							/>
							@error('phone')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group col-12 col-md-6">
							<label for="password">Password</label>
							<input type="password" name="password"
								   id="password" class="form-control password @error('password') is-invalid @enderror"
								   placeholder="Password" required
							/>
							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group col-12 col-md-6">
							<label for="password_confirmation">Password Confirmation</label>
							<input type="password" name="password_confirmation"
								   id="password_confirmation" class="form-control password @error('password_confirmation') is-invalid @enderror"
								   placeholder="Password Confirmation" required
							/>
							@error('password_confirmation')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group col-12">
							<button type="submit" class="btn btn-primary px-3 rounded-0">Sign Up <span class="ml-3 fa fa-long-arrow-right"></span></button>
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

		<div class="container mt-5">
			<div class="career-box">
				<div class="row">
					<div class="col-12 text-center py-2">
						Want to join our platform?
					</div>
					<div class="col-12 btn-group-lg text-center">
						<a href="{{ route('register.type' , [ 'type' => 'carer' ]) }}" class="btn btn-success">Become a Carer <span class="fa fa-long-arrow-right ml-3"></span></a>
						<a href="{{ route('register.type' , [ 'type' => 'housekeeper' ]) }}" class="btn btn-success">Become a Housekeeper <span class="fa fa-long-arrow-right ml-3"></span></a>
						<a href="{{ route('register.type' , [ 'type' => 'childminder' ]) }}" class="btn btn-success">Become a Childminder <span class="fa fa-long-arrow-right ml-3"></span></a>
						<a href="{{ route('register.type' , [ 'type' => 'client']) }}" class="btn btn-success">Become a Client <span class="fa fa-long-arrow-right ml-3"></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
