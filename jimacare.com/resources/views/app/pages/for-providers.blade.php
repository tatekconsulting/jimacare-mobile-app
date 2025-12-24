@extends('app.template.layout')

@section('content')
	{{-- Hero Section for Providers --}}
	<div class="hero-section">
		<div class="hero-overlay"></div>
		<div class="container">
			<div class="row align-items-center min-vh-75">
				<div class="col-12 col-lg-8 mx-auto text-center">
					<div class="hero-content">
						<h3 class="hero-subtitle">For Care Providers</h3>
						<h1 class="hero-title">Find Your Perfect Care Opportunity</h1>
						<p class="hero-description">Join thousands of care professionals across the UK. Browse available jobs, connect with clients, and grow your care business.</p>
						
						<div class="mt-4">
							<a href="{{ route('contract.index') }}" class="btn-search-modern" style="display: inline-block; text-decoration: none; margin-right: 15px;">
								<i class="fa fa-briefcase mr-2"></i> Browse Available Jobs
							</a>
							@guest
							<a href="{{ route('register.type', ['type' => 'carer']) }}" class="btn-search-modern" style="display: inline-block; text-decoration: none; background: rgba(255,255,255,0.2); border: 2px solid white;">
								<i class="fa fa-user-plus mr-2"></i> Sign Up as Provider
							</a>
							@endguest
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Trust Badges Section --}}
	<div class="trust-section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="trust-badges">
						<div class="trust-badge-item">
							<i class="fa fa-briefcase"></i>
							<span>Daily Job Updates</span>
						</div>
						<div class="trust-badge-item">
							<i class="fa fa-shield-alt"></i>
							<span>Insurance Included</span>
						</div>
						<div class="trust-badge-item">
							<i class="fa fa-money-bill-wave"></i>
							<span>Guaranteed Payments</span>
						</div>
						<div class="trust-badge-item">
							<i class="fa fa-star"></i>
							<span>Build Your Reputation</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- How It Works for Providers --}}
	<div class="how-it-works-section py-5">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center mb-5">
					<h2 class="section-title">How It Works for Providers</h2>
					<p class="section-subtitle">Start earning with quality care opportunities in 4 simple steps</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card">
						<div class="step-icon">
							<i class="fa fa-user-plus"></i>
						</div>
						<div class="step-number">1</div>
						<h4 class="step-title">Create Profile</h4>
						<p class="step-description">Sign up and create your professional profile. Upload documents, photos, and showcase your experience.</p>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card">
						<div class="step-icon">
							<i class="fa fa-search"></i>
						</div>
						<div class="step-number">2</div>
						<h4 class="step-title">Browse Jobs</h4>
						<p class="step-description">Search available jobs in your area. Filter by location, service type, and requirements.</p>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card">
						<div class="step-icon">
							<i class="fa fa-paper-plane"></i>
						</div>
						<div class="step-number">3</div>
						<h4 class="step-title">Apply & Connect</h4>
						<p class="step-description">Apply to jobs that match your skills. Message clients and schedule video interviews.</p>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card">
						<div class="step-icon">
							<i class="fa fa-check-circle"></i>
						</div>
						<div class="step-number">4</div>
						<h4 class="step-title">Get Hired & Work</h4>
						<p class="step-description">Get selected, track your hours with timesheets, and receive guaranteed payments.</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Benefits for Providers --}}
	<div class="why-choose-section py-5 bg-light">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-6 mb-4 mb-lg-0">
					<h2 class="section-title">Why Join JimaCare?</h2>
					<p class="lead">Build your care business with the UK's trusted care platform.</p>
					<div class="feature-list">
						<div class="feature-item">
							<i class="fa fa-check-circle text-success"></i>
							<div>
								<h5>No Sign-Up Fees</h5>
								<p>JimaCare advertises your profile to clients and there is no sign-up fee. Start earning immediately.</p>
							</div>
						</div>
						<div class="feature-item">
							<i class="fa fa-check-circle text-success"></i>
							<div>
								<h5>Guaranteed Payments</h5>
								<p>Payment arrangements are made by our JimaCare Payment Team. Your payments are guaranteed and secure.</p>
							</div>
						</div>
						<div class="feature-item">
							<i class="fa fa-check-circle text-success"></i>
							<div>
								<h5>Insurance Included</h5>
								<p>JimaCare organizes your carer insurance as long as you transact on the site. Work with peace of mind.</p>
							</div>
						</div>
						<div class="feature-item">
							<i class="fa fa-check-circle text-success"></i>
							<div>
								<h5>Daily Job Updates</h5>
								<p>New jobs are posted daily. Apply to opportunities that match your skills and availability.</p>
							</div>
						</div>
					</div>
					<div class="mt-4">
						<a href="{{ route('contract.index') }}" class="btn btn-primary btn-lg mr-3">
							Browse Jobs <i class="fa fa-arrow-right ml-2"></i>
						</a>
						@guest
						<a href="{{ route('register.type', ['type' => 'carer']) }}" class="btn btn-outline-primary btn-lg">
							Sign Up Now <i class="fa fa-user-plus ml-2"></i>
						</a>
						@endguest
					</div>
				</div>
				<div class="col-12 col-lg-6">
					<div class="verification-box">
						<h4 class="mb-4">Platform Features</h4>
						<div class="verification-steps">
							<div class="verification-step">
								<div class="step-icon-box">
									<i class="fa fa-clock"></i>
								</div>
								<div class="step-content">
									<h5>Timesheet Tracking</h5>
									<p>Easy clock in/out system with automatic timesheet generation for accurate payment.</p>
								</div>
							</div>
							<div class="verification-step">
								<div class="step-icon-box">
									<i class="fa fa-video"></i>
								</div>
								<div class="step-content">
									<h5>Video Calls</h5>
									<p>Connect with clients via secure video calls for interviews and consultations.</p>
								</div>
							</div>
							<div class="verification-step">
								<div class="step-icon-box">
									<i class="fa fa-comments"></i>
								</div>
								<div class="step-content">
									<h5>Direct Messaging</h5>
									<p>Communicate directly with clients through our secure messaging system.</p>
								</div>
							</div>
							<div class="verification-step">
								<div class="step-icon-box">
									<i class="fa fa-star"></i>
								</div>
								<div class="step-content">
									<h5>Build Your Reputation</h5>
									<p>Receive ratings and reviews from clients to build your professional reputation.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- CTA Section --}}
	<div class="cta-section py-5 bg-primary text-white">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-8 text-center text-lg-left mb-4 mb-lg-0">
					<h2 class="mb-2">Ready to Start Your Care Career?</h2>
					<p class="lead mb-0">Join thousands of care professionals earning with JimaCare</p>
				</div>
				<div class="col-12 col-lg-4">
					<div class="cta-buttons">
						@guest
						<a href="{{ route('register.type', ['type' => 'carer']) }}" class="btn btn-light btn-block btn-lg mb-2">
							<i class="fa fa-user-md mr-2"></i> Sign Up as Carer
						</a>
						<a href="{{ route('register.type', ['type' => 'childminder']) }}" class="btn btn-light btn-block btn-lg mb-2">
							<i class="fa fa-child mr-2"></i> Sign Up as Childminder
						</a>
						<a href="{{ route('register.type', ['type' => 'housekeeper']) }}" class="btn btn-light btn-block btn-lg">
							<i class="fa fa-broom mr-2"></i> Sign Up as Housekeeper
						</a>
						@else
						<a href="{{ route('contract.index') }}" class="btn btn-outline-light btn-block btn-lg">
							<i class="fa fa-briefcase mr-2"></i> Browse Jobs Now
						</a>
						@endguest
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Custom Styles --}}
	@include('app.pages.partials.home-styles')
@endsection

