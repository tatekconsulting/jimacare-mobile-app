@extends('app.template.auth-layout')

@section('content')
	<div class="login-page">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-12 col-12 order-2 order-sm-2 order-md-1">
					<h4>Login</h4>
					<form method="POST" action="{{ route('login') }}">
						@csrf

						<div class="form-group">
							<label for="email">Email </label>
							<input type="email" name="email" value="{{ old('email') }}"
								   id="email" class="form-control email @error('email') is-invalid @enderror"
								   placeholder="Email Address" required autofocus
							/>
							@error('email')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group">
							<label for="password">Password <a href="{{ route('password.request') }}">Forgot password?</a></label>
							<input type="password" name="password" value="{{ old('password') }}"
								   id="password" class="form-control password @error('password') is-invalid @enderror"
								   placeholder="Password" required
							/>
							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-primary px-3 rounded-0">Login <span class="ml-3 fa fa-long-arrow-right"></span></button>
						</div>
					</form>
				</div>
				<div class="col-md-6 col-sm-12 col-12 order-1 order-sm-1 order-md-2">
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
