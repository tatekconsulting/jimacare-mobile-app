@extends('app.template.auth-layout')

@section('content')
	<div class="login-page pb-5">
		<div class="container">
			<div class="row">
				<div class="col-12 col-md-7">
					<h2 class="d-block w-100">{{ __('Verify Your Email Address') }}</h2>

					@if (session('resent'))
						<div class="alert alert-success" role="alert">
							{{ __('A fresh verification link has been sent to your email address.') }}
						</div>
					@endif

					<p class="d-block pt-3 pb-1">
						{{ __('Before proceeding, please check your email for a verification link.') }}<br />
						{{ __('If you did not receive the email') }},
					</p>

					<form class="d-inline" method="POST" action="{{ route('verification.send') }}">
						@csrf
						<button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('Click here to request another') }}</button>.
					</form>
				</div>
				<div class="col-12 col-md-5">
					<div class="login-img">
						<img src="{{ asset('img/login-image.png') }}" alt="">
					</div>
				</div>
			</div>
		</div>
		{{--<div class="container mt-5">
			<div class="career-box">
				<div class="row">
					<div class="col-12 col-lg-6 text-center text-lg-left py-2">
						Want to join our platform?
					</div>
					<div class="col-12 col-lg-6 text-center text-lg-right">
						<a href="{{ route('register.type' , [ 'type' => 'carer' ]) }}" class="btn btn-lg btn-success mr-3 my-1">Become Seller <span class="fa fa-long-arrow-right ml-3"></span></a>
						<a href="{{ route('register.type' , [ 'type' => 'client' ]) }}" class="btn btn-lg btn-success my-1">Become Customer <span class="fa fa-long-arrow-right ml-3"></span></a>
					</div>
				</div>
			</div>
		</div>--}}
	</div>
@endsection
