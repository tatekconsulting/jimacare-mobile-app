<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="user-token" content="{{ auth()->id() ?? '0' }}">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
		<title>{{ config('app.name', 'Laravel') }}</title>
		<link href="{{ asset('css/app.css') }}" rel="stylesheet">
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAemE-RYiok4C3WvIOuLLo3nhmMNaffl6s&libraries=places&ver=1.0"></script>
	</head>
	<body>
		@include('app.template.header')
		<div class="container-fluid">
			<div class="row">
				<aside class="col-auto p-0" style="width: 300px;">
					<div class="side-inner">
						{{--<div class="profile">
							<img src="images/person_4.jpg" alt="Image" class="img-fluid">
							<h3 class="name">Craig David</h3>
							<span class="country">Web Designer</span>
						</div>--}}
						<div class="nav-menu">
							<ul>
								<li>
									<a href="{{ route('profile') }}">My Profile</a>
								</li>
								<li>
									<a href="{{ route('photo') }}">Upload a Photo</a>
								</li>
								@if(auth()->user()->role->seller == true)
									<li>
										<a href="{{ route('video') }}">Upload a Video</a>
									</li>
									<li>
										<a href="{{ route('documents') }}">My Documents</a>
									</li>
								@endif
								<li>
									<a href="{{ route('inbox') }}">My Messages</a>
								</li>

								@if(auth()->user()->role->slug == 'client')
									<li class="accordion">
										<a href="#" data-toggle="collapse" data-target="#collapseOne"
										   aria-expanded="false" aria-controls="collapseOne" class="collapsible">
											Post a Job
										</a>
										<div id="collapseOne" class="collapse" aria-labelledby="headingOne">
											<div>
												<ul>
													@foreach(\App\Models\Role::where('seller', true)->get() as $r)
														<li>
															<a href="{{ route('contract.create', ['type' => $r->slug]).'?tab=requirements' }}">For {{ $r->title }}</a>
														</li>
													@endforeach
												</ul>
											</div>
										</div>
									</li>
								@endif

								<li>
									<a href="{{ route('ratings') }}">My Ratings</a>
								</li>
								<li>
									<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('side-logout').submit();">
										Logout
									</a>

									<form id="side-logout" action="{{ route('logout') }}" method="POST" class="d-none">
										@csrf
									</form>
								</li>
							</ul>
						</div>
					</div>
				</aside>
				<main class="col p-5">
					@if(session()->has('notice'))
						<div class='alert alert-success alert-notice alert-dismissible fade show' role='alert'>
							{{ session('notice') }}
							<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
								<span aria-hidden='true'>&times;</span>
							</button>
						</div>
					@endif

					@if($errors->any())
						<ul class='alert alert-danger alert-notice alert-dismissible fade show' role='alert'>
							<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
								<span aria-hidden='true'>&times;</span>
							</button>
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					@endif

					@yield('content')
				</main>
			</div>
		</div>

		@include('app.template.footer')
		<script src="{{ asset('js/app.js') }}"></script>
	</body>
</html>
