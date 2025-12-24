<header class="header-enhanced">
	<div class="container">
		{{-- Top Bar with Dual Navigation --}}
		<div class="header-top-bar">
			<div class="dual-nav-tabs-top">
				<ul class="nav nav-tabs dual-nav-top" role="tablist">
					<li class="nav-item">
						<a class="nav-link {{ request()->routeIs('for.clients') || (request()->routeIs('home') && !request()->routeIs('for.providers')) ? 'active' : '' }}" 
						   href="{{ route('for.clients') }}">
							<i class="fa fa-users mr-2"></i>For Clients
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{ request()->routeIs('for.providers') ? 'active' : '' }}" 
						   href="{{ route('for.providers') }}">
							<i class="fa fa-user-md mr-2"></i>For Providers
						</a>
					</li>
				</ul>
			</div>
		</div>
		
		{{-- Main Navigation Bar --}}
		<nav class="navbar navbar-expand-lg navbar-main">
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
				{{-- Dual Navigation for Mobile --}}
				<div class="dual-nav-mobile">
					<ul class="nav nav-tabs dual-nav-top" role="tablist">
						<li class="nav-item">
							<a class="nav-link {{ request()->routeIs('for.clients') || (request()->routeIs('home') && !request()->routeIs('for.providers')) ? 'active' : '' }}" 
							   href="{{ route('for.clients') }}">
								<i class="fa fa-users mr-2"></i>For Clients
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link {{ request()->routeIs('for.providers') ? 'active' : '' }}" 
							   href="{{ route('for.providers') }}">
								<i class="fa fa-user-md mr-2"></i>For Providers
							</a>
						</li>
					</ul>
				</div>
				
				<ul class="navbar-nav mx-auto">
					<li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
						<a class="nav-link" href="{{ route('home') }}">Home</a>
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
					@auth
						@php
							$userRole = auth()->user()->role->slug ?? '';
							$isAdmin = auth()->user()->role_id == 1 || $userRole === 'admin';
							$isClient = $userRole === 'client';
							$isServiceProvider = in_array($userRole, ['carer', 'childminder', 'housekeeper']);
						@endphp
						@if($isClient || $isAdmin)
							<li class="nav-item">
								<a class="nav-link" href="{{ route('sellers') }}">Find Carers</a>
							</li>
						@endif
						@if($isServiceProvider || $isAdmin)
							<li class="nav-item">
								<a class="nav-link" href="{{ route('contract.index') }}">Browse Jobs</a>
							</li>
						@endif
					@else
						<li class="nav-item">
							<a class="nav-link" href="{{ route('contract.index') }}">Find Jobs</a>
						</li>
					@endauth
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
				<a class="dropdown-item" href="{{ route('compliance.index') }}">
					<i class="fa fa-shield-alt mr-2"></i>Compliance & Certifications
				</a>
				<a class="dropdown-item" href="{{ route('inbox') }}">My Messages</a>
				<a class="dropdown-item" href="{{ route('ratings') }}">My Ratings</a>
				
				<div class="dropdown-divider"></div>
				
				{{-- New Features Section --}}
				@php
					$userRole = auth()->user()->role->slug ?? '';
					$isAdmin = $userRole === 'admin' || auth()->user()->role_id == 1;
					$isCarer = in_array($userRole, ['carer', 'childminder', 'housekeeper']);
					$isClient = $userRole === 'client';
					$unreadCount = 0;
					try {
						$unreadCount = \App\Models\UserNotification::where('user_id', auth()->id())->where('is_read', false)->count();
					} catch (\Exception $e) {
						// Table or column doesn't exist yet - migration not run
						$unreadCount = 0;
					}
				@endphp
				
				{{-- Notifications with badge --}}
				<a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ url('/notifications') }}">
					<span>🔔 Notifications</span>
					@if($unreadCount > 0)
						<span class="badge badge-danger badge-pill">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
					@endif
				</a>
				
				{{-- Job Applications --}}
				@if($isClient || $isAdmin)
					<a class="dropdown-item" href="{{ url('/job-applications') }}">📋 Job Applications</a>
				@endif
				@if($isCarer || $isAdmin)
					<a class="dropdown-item" href="{{ url('/my-applications') }}">📋 My Applications</a>
				@endif
				@if($isAdmin)
					<a class="dropdown-item" href="{{ url('/job-applications') }}">📋 All Applications (Admin)</a>
				@endif
				
				{{-- Timesheets --}}
				@if($isClient || $isAdmin)
					<a class="dropdown-item" href="{{ url('/client/timesheets') }}">⏱️ Review Timesheets</a>
				@endif
				@if($isCarer || $isAdmin)
					<a class="dropdown-item" href="{{ url('/carer/timesheets') }}">⏱️ My Timesheets</a>
				@endif
				@if($isAdmin)
					<a class="dropdown-item" href="{{ route('dashboard.timesheets.index') }}">⏱️ All Timesheets (Admin)</a>
				@endif
				
				{{-- Payments (for Clients) --}}
				@if($isClient || $isAdmin)
					<a class="dropdown-item" href="{{ route('timesheet-payments.index') }}">💳 Timesheet Payments</a>
				@endif
				
				<div class="dropdown-divider"></div>
				
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

{{-- Enhanced Header Styles --}}
<style>
	/* Header Container */
	.header-enhanced {
		background: #fff;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
		position: sticky;
		top: 0;
		z-index: 1000;
	}
	
	/* Top Bar with Dual Navigation */
	.header-top-bar {
		background: #f8f9fa;
		border-bottom: 1px solid #e0e0e0;
		padding: 0;
	}
	
	.dual-nav-tabs-top {
		display: flex;
		justify-content: center;
		width: 100%;
	}
	
	.dual-nav-top {
		border: none;
		display: flex;
		justify-content: center;
		width: 100%;
		margin: 0;
		padding: 0;
	}
	
	.dual-nav-top .nav-item {
		margin: 0;
	}
	
	.dual-nav-top .nav-link {
		color: #1E3748;
		font-weight: 600;
		padding: 12px 30px;
		border: none;
		border-bottom: 3px solid transparent;
		background: transparent;
		transition: all 0.3s ease;
		border-radius: 0;
		font-size: 14px;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}
	
	.dual-nav-top .nav-link:hover {
		color: #D84727;
		background: rgba(216, 71, 39, 0.05);
		border-bottom-color: rgba(216, 71, 39, 0.3);
	}
	
	.dual-nav-top .nav-link.active {
		color: #D84727;
		border-bottom-color: #D84727;
		background: rgba(216, 71, 39, 0.08);
		font-weight: 700;
	}
	
	.dual-nav-top .nav-link i {
		margin-right: 6px;
		font-size: 13px;
	}
	
	/* Main Navigation Bar */
	.navbar-main {
		padding: 15px 0;
		background: #fff;
		position: relative;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}
	
	.navbar-main .navbar-nav {
		display: flex;
		align-items: center;
		gap: 0;
	}
	
	.navbar-main .nav-item {
		margin: 0 8px;
	}
	
	.navbar-main .nav-link {
		color: #1E3748;
		font-weight: 500;
		font-size: 15px;
		padding: 8px 15px;
		transition: all 0.3s ease;
		border-radius: 6px;
		position: relative;
	}
	
	.navbar-main .nav-link:hover {
		color: #D84727;
		background: rgba(216, 71, 39, 0.05);
	}
	
	.navbar-main .nav-item.active .nav-link {
		color: #D84727;
		font-weight: 600;
	}
	
	.navbar-main .nav-item.active .nav-link::after {
		content: '';
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translateX(-50%);
		width: 30px;
		height: 2px;
		background: #D84727;
		border-radius: 2px;
	}
	
	/* Brand Styling */
	.navbar-brand {
		display: flex;
		align-items: center;
		gap: 10px;
		font-size: 12px;
		color: #1E3748;
		font-weight: 600;
		text-decoration: none;
	}
	
	.navbar-brand img {
		height: 50px;
		width: auto;
	}
	
	.navbar-brand span {
		display: block;
		line-height: 1.2;
	}
	
	/* User Menu */
	.nav-link.dropdown-toggle {
		display: flex;
		align-items: center;
		gap: 10px;
		padding: 8px 15px;
		border-radius: 8px;
		transition: all 0.3s ease;
	}
	
	.nav-link.dropdown-toggle:hover {
		background: rgba(216, 71, 39, 0.05);
	}
	
	.img-profile {
		width: 36px;
		height: 36px;
		object-fit: cover;
		border: 2px solid #e0e0e0;
	}
	
	/* Login/Register Buttons */
	.btn.btn-md.btn-danger {
		padding: 10px 20px;
		border-radius: 8px;
		font-weight: 600;
		transition: all 0.3s ease;
	}
	
	.btn.btn-md.btn-danger:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(216, 71, 39, 0.3);
	}
	
	/* UK Icon */
	.navbar-main {
		position: relative;
	}
	
	.uk-icon {
		position: absolute;
		top: 50%;
		right: 15px;
		transform: translateY(-50%);
		width: 42px;
		height: auto;
		z-index: 10;
	}
	
	@media (max-width: 991px) {
		.uk-icon {
			position: static;
			transform: none;
			margin-left: auto;
		}
	}
	
	/* Responsive Design */
	@media (max-width: 991px) {
		.header-top-bar {
			display: none;
		}
		
		.navbar-main {
			padding: 10px 0;
		}
		
		.navbar-main .navbar-nav {
			flex-direction: column;
			width: 100%;
			margin-top: 15px;
		}
		
		.navbar-main .nav-item {
			margin: 5px 0;
			width: 100%;
		}
		
		.navbar-main .nav-link {
			padding: 12px 20px;
			width: 100%;
			text-align: left;
		}
		
		.navbar-main .nav-item.active .nav-link::after {
			display: none;
		}
		
		/* Show dual nav in mobile menu */
		.navbar-collapse .dual-nav-mobile {
			display: block;
			margin-bottom: 15px;
			padding-bottom: 15px;
			border-bottom: 2px solid #e0e0e0;
		}
		
		.dual-nav-mobile .nav {
			flex-direction: column;
		}
		
		.dual-nav-mobile .nav-item {
			width: 100%;
			margin: 5px 0;
		}
		
		.dual-nav-mobile .nav-link {
			text-align: center;
			padding: 12px 20px;
		}
	}
	
	@media (min-width: 992px) {
		.dual-nav-mobile {
			display: none;
		}
	}
	
	/* Mobile Menu Toggle */
	.navbar-toggler {
		border: none;
		padding: 5px;
	}
	
	.navbar-toggler:focus {
		outline: 2px solid #D84727;
		outline-offset: 2px;
	}
</style>

