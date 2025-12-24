<header class="py-3">
	<div class="container">
		<nav class="navbar navbar-expand-lg">
			<a class="navbar-brand" href="{{route('home')}}">
				<img src="{{ asset('img/logo.png') }}" alt="{{config('app.name')}} Logo" width="80px">
				<span>Affordable & Quality Care</span>
			</a>

			<button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse"
					aria-expanded="false" aria-label="Toggle navigation">
				<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img" focusable="false">
					<title>Menu</title>
					<path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"></path>
				</svg>
			</button>
			<div class="navbar-collapse collapse" id="navbarCollapse">
				<ul class="navbar-nav m-auto">
					<li class="nav-item active">
						<a class="nav-link" href="{{ route('home') }}">Home <span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('about') }}">About</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('how-it-works') }}">Things to know</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('team') }}">Team</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('helpdesk') }}">Helpdesk</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('blog') }}">Blog</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ route('contract.index') }}">Find Jobs</a>
					</li>
				</ul>
			</div>
			@auth
				<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</span>
					<img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}" height="36">
				</a>
				<div class="text-left dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
					@if(auth()->user()->role_id===1)
						<a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
					@endif
					<a class="dropdown-item" href="{{ route('profile') }}">My Profile</a>
					<a class="dropdown-item" href="{{ route('photo') }}">Upload a Photo</a>
					<a class="dropdown-item" href="{{ route('video') }}">Upload a Video</a>
					<a class="dropdown-item" href="{{ route('documents') }}">My Documents</a>
					<a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>
					<a class="dropdown-item" href="{{ route('inbox') }}">My Messages</a>
					<a class="dropdown-item" href="{{ route('ratings') }}">My Ratings</a>
					<a href="{{ route('logout') }}"
					   class="text-left dropdown-item"
					   onclick="event.preventDefault(); document.getElementById('logout').submit();"
					>Logout</a>
					<form id="logout" action="{{ route('logout') }}" method="POST" class="d-none">
						@csrf
					</form>
				</div>
			@else
				<a class="btn btn-md btn-danger px-md-4 mr-2" href="{{ route('login') }}">
					<span class="mr-2 d-inline text-white small">Login</span>
				</a>

				<a class="btn btn-md btn-danger dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
				   aria-expanded="false">
					<span class="mr-2 d-inline text-white small">Register</span>
				</a>
				<div class="text-left dropdown-menu dropdown-menu-right shadow animated--grow-in bg-danger" aria-labelledby="userDropdown">
					@foreach(\App\Models\Role::where('active', true)->get() as $role)
						<a class="dropdown-item text-white" href="{{ route('register.type', ['type' => $role->slug]) }}">Register As {{ $role->title }}</a>
					@endforeach
				</div>
			@endauth
			<img class="uk-icon" src="{{ asset('img/uk-icon.png') }}" alt="{{config('app.name')}} UK icon" width="50">
		</nav>
	</div>
</header>
