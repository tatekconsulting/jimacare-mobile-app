@extends('app.template.layout')

@section('content')
	{{-- Hero Section for Clients - Enhanced UX --}}
	<div class="hero-section-client">
		<div class="hero-overlay-client"></div>
		<div class="container">
			<div class="row align-items-center min-vh-80">
				<div class="col-12 col-lg-10 mx-auto">
					<div class="hero-content-client">
						<div class="hero-badge">
							<i class="fa fa-heart mr-2"></i> Trusted by 10,000+ Families
						</div>
						<h3 class="hero-subtitle-client">For Clients & Families</h3>
						<h1 class="hero-title-client">Find Trusted Care Professionals Near You</h1>
						<p class="hero-description-client">Connect with verified carers, childminders, and housekeepers. All professionals are DBS checked, interviewed, and ready to provide quality care.</p>
						
						{{-- Enhanced Search Form with Better UX --}}
						<div class="search-box-enhanced">
							<form action="{{ route('sellers') }}" method="GET" class="search-form-enhanced" id="clientSearchForm">
								<div class="row g-3">
									<div class="col-12 col-md-5">
										<div class="form-group-enhanced">
											<label class="form-label-enhanced">
												<i class="fa fa-user-md"></i> I'm Looking For
											</label>
											<select name="type" class="form-control-enhanced" required>
												<option value="">Select Service Type</option>
												@foreach($roles as $role)
													<option value="{{ $role->id }}" @if(request('type') == $role->id) selected @endif>
														{{ $role->title }}
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-12 col-md-5">
										<div class="form-group-enhanced">
											<label class="form-label-enhanced">
												<i class="fa fa-map-marker-alt"></i> Location
											</label>
											<input type="text" name="address" id="address-client" 
												   class="form-control-enhanced address" 
												   placeholder="Enter postcode or town" 
												   required
												   autocomplete="off">
											<input type="hidden" name="lat" class="lat" value="{{ request('lat') }}"/>
											<input type="hidden" name="long" class="long" value="{{ request('long') }}"/>
										</div>
									</div>
									<div class="col-12 col-md-2">
										<button type="submit" class="btn-search-enhanced">
											<i class="fa fa-search mr-2"></i> Search
										</button>
									</div>
								</div>
							</form>
							
							{{-- Popular Locations with Better UX --}}
							<div class="popular-locations-enhanced">
								<small class="popular-locations-label">
									<i class="fa fa-fire mr-1"></i> Popular Searches:
								</small>
								<div class="location-tags-enhanced">
									<a href="{{route('sellers')}}?type=3&address=London%2C+UK&lat=51.5073509&long=-0.1277583" 
									   class="location-tag-enhanced">
										<i class="fa fa-map-marker-alt"></i> Carers in London
									</a>
									<a href="{{route('sellers')}}?type=4&address=London%2C+UK&lat=51.5073509&long=-0.1277583" 
									   class="location-tag-enhanced">
										<i class="fa fa-map-marker-alt"></i> Childminders in London
									</a>
									<a href="{{route('sellers')}}?type=5&address=London%2C+UK&lat=51.5073509&long=-0.1277583" 
									   class="location-tag-enhanced">
										<i class="fa fa-map-marker-alt"></i> Housekeepers in London
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Trust Badges Section - Enhanced --}}
	<div class="trust-section-enhanced">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="trust-badges-enhanced">
						<div class="trust-badge-enhanced">
							<div class="trust-icon-wrapper">
								<i class="fa fa-shield-alt"></i>
							</div>
							<span class="trust-text">Enhanced DBS Checked</span>
						</div>
						<div class="trust-badge-enhanced cqc-enhanced">
							<div class="trust-icon-wrapper">
								@php
									$cqcLogoPath = 'img/cqc-logo.png';
									$cqcLogoExists = file_exists(public_path($cqcLogoPath));
								@endphp
								@if($cqcLogoExists)
									<img src="{{ asset($cqcLogoPath) }}" alt="CQC Registered" class="cqc-logo-enhanced"/>
								@else
									<i class="fa fa-certificate"></i>
								@endif
							</div>
							<span class="trust-text">CQC Registered</span>
						</div>
						<div class="trust-badge-enhanced">
							<div class="trust-icon-wrapper">
								<i class="fa fa-user-check"></i>
							</div>
							<span class="trust-text">Verified Professionals</span>
						</div>
						<div class="trust-badge-enhanced">
							<div class="trust-icon-wrapper">
								<i class="fa fa-star"></i>
							</div>
							<span class="trust-text">5-Star Rated Service</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- How It Works for Clients - Enhanced UX --}}
	<div class="how-it-works-enhanced py-5">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center mb-5">
					<div class="section-header">
						<h2 class="section-title-enhanced">How It Works for Clients</h2>
						<p class="section-subtitle-enhanced">Find the perfect care professional in 4 simple steps</p>
						<div class="section-divider"></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card-enhanced">
						<div class="step-icon-enhanced">
							<i class="fa fa-user-plus"></i>
						</div>
						<div class="step-number-enhanced">1</div>
						<h4 class="step-title-enhanced">Sign Up Free</h4>
						<p class="step-description-enhanced">Create your account in minutes. No credit card required.</p>
						<div class="step-arrow">
							<i class="fa fa-arrow-right"></i>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card-enhanced">
						<div class="step-icon-enhanced">
							<i class="fa fa-search"></i>
						</div>
						<div class="step-number-enhanced">2</div>
						<h4 class="step-title-enhanced">Search & Browse</h4>
						<p class="step-description-enhanced">Search by location and service type. View detailed profiles with reviews.</p>
						<div class="step-arrow">
							<i class="fa fa-arrow-right"></i>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card-enhanced">
						<div class="step-icon-enhanced">
							<i class="fa fa-comments"></i>
						</div>
						<div class="step-number-enhanced">3</div>
						<h4 class="step-title-enhanced">Message & Connect</h4>
						<p class="step-description-enhanced">Chat with providers, ask questions, and schedule interviews via video call.</p>
						<div class="step-arrow">
							<i class="fa fa-arrow-right"></i>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card-enhanced">
						<div class="step-icon-enhanced">
							<i class="fa fa-handshake"></i>
						</div>
						<div class="step-number-enhanced">4</div>
						<h4 class="step-title-enhanced">Hire & Get Started</h4>
						<p class="step-description-enhanced">Choose your provider, finalize details, and begin receiving quality care.</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Why Choose Us Section - Enhanced UX --}}
	<div class="why-choose-enhanced py-5">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-6 mb-5 mb-lg-0">
					<div class="why-choose-content">
						<h2 class="section-title-enhanced mb-4">Why Choose JimaCare?</h2>
						<p class="lead-enhanced mb-4">We make it easy to find experienced, trustworthy care professionals who match your specific needs.</p>
						<div class="feature-list-enhanced">
							<div class="feature-item-enhanced">
								<div class="feature-icon-wrapper">
									<i class="fa fa-check-circle"></i>
								</div>
								<div class="feature-content">
									<h5 class="feature-title">Fully Verified Professionals</h5>
									<p class="feature-description">All providers undergo thorough background checks, interviews, and identity verification for your peace of mind.</p>
								</div>
							</div>
							<div class="feature-item-enhanced">
								<div class="feature-icon-wrapper">
									<i class="fa fa-check-circle"></i>
								</div>
								<div class="feature-content">
									<h5 class="feature-title">Flexible Matching</h5>
									<p class="feature-description">Whether you need someone who speaks a foreign language, drives, or has specialist skills, we help you find the perfect match.</p>
								</div>
							</div>
							<div class="feature-item-enhanced">
								<div class="feature-icon-wrapper">
									<i class="fa fa-check-circle"></i>
								</div>
								<div class="feature-content">
									<h5 class="feature-title">Secure Payment System</h5>
									<p class="feature-description">Safe and transparent payment processing with detailed timesheet tracking and invoicing.</p>
								</div>
							</div>
						</div>
						<div class="cta-buttons-enhanced mt-4">
							<a href="{{ route('contract.create') }}" class="btn btn-primary-enhanced btn-lg mr-3 mb-3 mb-md-0">
								<i class="fa fa-briefcase mr-2"></i> Post a Job
							</a>
							@guest
							<a href="{{ route('register.type', ['type' => 'client']) }}" class="btn btn-outline-primary-enhanced btn-lg">
								<i class="fa fa-user-plus mr-2"></i> Sign Up as Client
							</a>
							@endguest
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6">
					<div class="verification-box-enhanced">
						<div class="verification-header">
							<h4 class="verification-title">
								<i class="fa fa-shield-alt mr-2"></i>Our Verification Process
							</h4>
							<p class="verification-subtitle">Every provider is thoroughly vetted</p>
						</div>
						<div class="verification-steps-enhanced">
							<div class="verification-step-enhanced">
								<div class="step-icon-box-enhanced">
									<i class="fa fa-file-alt"></i>
								</div>
								<div class="step-content-enhanced">
									<h5 class="step-title-verification">Reference Check</h5>
									<p class="step-description-verification">We confirm suitability through previous employer references.</p>
								</div>
							</div>
							<div class="verification-step-enhanced">
								<div class="step-icon-box-enhanced">
									<i class="fa fa-comments"></i>
								</div>
								<div class="step-content-enhanced">
									<h5 class="step-title-verification">Interview Process</h5>
									<p class="step-description-verification">Every provider is interviewed to assess skills, experience, and reliability.</p>
								</div>
							</div>
							<div class="verification-step-enhanced">
								<div class="step-icon-box-enhanced">
									<i class="fa fa-shield-alt"></i>
								</div>
								<div class="step-content-enhanced">
									<h5 class="step-title-verification">Enhanced DBS Check</h5>
									<p class="step-description-verification">Full background checks guarantee trust and safety for your loved ones.</p>
								</div>
							</div>
							<div class="verification-step-enhanced">
								<div class="step-icon-box-enhanced">
									<i class="fa fa-id-card"></i>
								</div>
								<div class="step-content-enhanced">
									<h5 class="step-title-verification">Identity Verification</h5>
									<p class="step-description-verification">Facial recognition and document verification ensure authenticity.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Stats Section - New Addition for Trust --}}
	<div class="stats-section py-5 bg-light">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center mb-5">
					<h3 class="stats-title">Trusted by Thousands</h3>
				</div>
			</div>
			<div class="row">
				<div class="col-6 col-md-3 mb-4">
					<div class="stat-card">
						<div class="stat-number">10,000+</div>
						<div class="stat-label">Happy Families</div>
					</div>
				</div>
				<div class="col-6 col-md-3 mb-4">
					<div class="stat-card">
						<div class="stat-number">5,000+</div>
						<div class="stat-label">Verified Providers</div>
					</div>
				</div>
				<div class="col-6 col-md-3 mb-4">
					<div class="stat-card">
						<div class="stat-number">50,000+</div>
						<div class="stat-label">Hours of Care</div>
					</div>
				</div>
				<div class="col-6 col-md-3 mb-4">
					<div class="stat-card">
						<div class="stat-number">4.9/5</div>
						<div class="stat-label">Average Rating</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- CTA Section - Enhanced UX --}}
	<div class="cta-section-enhanced py-5">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-8 text-center text-lg-left mb-4 mb-lg-0">
					<h2 class="cta-title">Ready to Find Your Perfect Care Provider?</h2>
					<p class="cta-description">Join thousands of families who trust JimaCare for quality care services</p>
				</div>
				<div class="col-12 col-lg-4">
					<div class="cta-buttons-final">
						@guest
						<a href="{{ route('register.type', ['type' => 'client']) }}" class="btn btn-light-enhanced btn-block btn-lg mb-3">
							<i class="fa fa-user-plus mr-2"></i> Sign Up as Client
						</a>
						@endguest
						<a href="{{ route('sellers') }}" class="btn btn-outline-light-enhanced btn-block btn-lg">
							<i class="fa fa-search mr-2"></i> Browse Care Providers
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Enhanced Styles for Better UX --}}
	<style>
		/* Hero Section - Enhanced */
		.hero-section-client {
			position: relative;
			background: linear-gradient(135deg, #1E3748 0%, #2c5f7d 50%, #D84727 100%);
			background-image: url('{{ asset("img/slider-banner.png") }}');
			background-size: cover;
			background-position: center;
			background-attachment: fixed;
			padding: 120px 0 100px;
			min-height: 85vh;
			display: flex;
			align-items: center;
			overflow: hidden;
		}
		
		.hero-section-client::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: radial-gradient(circle at 30% 50%, rgba(216, 71, 39, 0.3) 0%, transparent 50%);
			z-index: 1;
		}
		
		.hero-overlay-client {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: linear-gradient(135deg, rgba(30, 55, 72, 0.88) 0%, rgba(44, 95, 125, 0.85) 50%, rgba(216, 71, 39, 0.82) 100%);
			z-index: 1;
		}
		
		.hero-content-client {
			position: relative;
			z-index: 2;
			animation: fadeInUp 0.8s ease-out;
		}
		
		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		.hero-badge {
			display: inline-block;
			background: rgba(255, 255, 255, 0.2);
			backdrop-filter: blur(10px);
			border: 1px solid rgba(255, 255, 255, 0.3);
			color: #fff;
			padding: 8px 20px;
			border-radius: 50px;
			font-size: 14px;
			font-weight: 600;
			margin-bottom: 20px;
			animation: pulse 2s ease-in-out infinite;
		}
		
		@keyframes pulse {
			0%, 100% { transform: scale(1); }
			50% { transform: scale(1.05); }
		}
		
		.hero-subtitle-client {
			color: #fff;
			font-size: 16px;
			font-weight: 500;
			text-transform: uppercase;
			letter-spacing: 2px;
			margin-bottom: 15px;
			opacity: 0.95;
		}
		
		.hero-title-client {
			color: #fff;
			font-size: 56px;
			font-weight: 800;
			line-height: 1.15;
			margin-bottom: 25px;
			text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
		}
		
		.hero-description-client {
			color: rgba(255, 255, 255, 0.95);
			font-size: 20px;
			line-height: 1.6;
			margin-bottom: 45px;
			max-width: 700px;
			margin-left: auto;
			margin-right: auto;
		}
		
		.min-vh-80 {
			min-height: 80vh;
		}

		/* Search Box - Enhanced UX */
		.search-box-enhanced {
			background: rgba(255, 255, 255, 0.98);
			backdrop-filter: blur(10px);
			border-radius: 24px;
			padding: 35px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
			margin-top: 40px;
			transition: all 0.3s ease;
		}
		
		.search-box-enhanced:hover {
			box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.2);
		}
		
		.form-group-enhanced {
			margin-bottom: 0;
		}
		
		.form-label-enhanced {
			display: block;
			font-size: 13px;
			font-weight: 700;
			color: #1E3748;
			margin-bottom: 10px;
			text-transform: uppercase;
			letter-spacing: 0.8px;
		}
		
		.form-label-enhanced i {
			margin-right: 6px;
			color: #D84727;
			font-size: 14px;
		}
		
		.form-control-enhanced {
			width: 100%;
			padding: 16px 18px;
			border: 2px solid #e8e8e8;
			border-radius: 12px;
			font-size: 16px;
			transition: all 0.3s ease;
			background: #fff;
		}
		
		.form-control-enhanced:focus {
			border-color: #D84727;
			outline: none;
			box-shadow: 0 0 0 4px rgba(216, 71, 39, 0.15);
			transform: translateY(-2px);
		}
		
		.btn-search-enhanced {
			width: 100%;
			padding: 16px 24px;
			background: linear-gradient(135deg, #D84727 0%, #c0392b 100%);
			color: #fff;
			border: none;
			border-radius: 12px;
			font-size: 16px;
			font-weight: 700;
			cursor: pointer;
			transition: all 0.3s ease;
			box-shadow: 0 4px 15px rgba(216, 71, 39, 0.3);
			display: flex;
			align-items: center;
			justify-content: center;
		}
		
		.btn-search-enhanced:hover {
			transform: translateY(-3px);
			box-shadow: 0 6px 20px rgba(216, 71, 39, 0.4);
			background: linear-gradient(135deg, #c0392b 0%, #D84727 100%);
		}
		
		.btn-search-enhanced:active {
			transform: translateY(-1px);
		}
		
		.popular-locations-enhanced {
			margin-top: 25px;
			padding-top: 25px;
			border-top: 2px solid #f0f0f0;
		}
		
		.popular-locations-label {
			display: block;
			color: #666;
			font-weight: 600;
			margin-bottom: 12px;
			font-size: 13px;
		}
		
		.location-tags-enhanced {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
		}
		
		.location-tag-enhanced {
			display: inline-flex;
			align-items: center;
			padding: 10px 18px;
			background: #f8f9fa;
			color: #1E3748;
			border-radius: 25px;
			font-size: 14px;
			font-weight: 600;
			text-decoration: none;
			transition: all 0.3s ease;
			border: 2px solid transparent;
		}
		
		.location-tag-enhanced i {
			margin-right: 6px;
			color: #D84727;
			font-size: 12px;
		}
		
		.location-tag-enhanced:hover {
			background: #D84727;
			color: #fff;
			text-decoration: none;
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(216, 71, 39, 0.3);
		}
		
		.location-tag-enhanced:hover i {
			color: #fff;
		}

		/* Trust Section - Enhanced */
		.trust-section-enhanced {
			background: #fff;
			padding: 50px 0;
			box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.05);
		}
		
		.trust-badges-enhanced {
			display: flex;
			justify-content: space-around;
			flex-wrap: wrap;
			gap: 30px;
		}
		
		.trust-badge-enhanced {
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 12px;
			padding: 20px;
			border-radius: 16px;
			background: #f8f9fa;
			transition: all 0.3s ease;
			min-width: 180px;
		}
		
		.trust-badge-enhanced:hover {
			background: #fff;
			transform: translateY(-5px);
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
		}
		
		.trust-icon-wrapper {
			width: 60px;
			height: 60px;
			background: linear-gradient(135deg, #D84727 0%, #c0392b 100%);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
			font-size: 24px;
		}
		
		.trust-text {
			font-weight: 700;
			color: #1E3748;
			font-size: 14px;
			text-align: center;
		}
		
		.cqc-logo-enhanced {
			height: 50px;
			width: auto;
			max-width: 120px;
			object-fit: contain;
		}

		/* Section Titles - Enhanced */
		.section-header {
			margin-bottom: 50px;
		}
		
		.section-title-enhanced {
			font-size: 42px;
			font-weight: 800;
			color: #1E3748;
			margin-bottom: 15px;
			line-height: 1.2;
		}
		
		.section-subtitle-enhanced {
			font-size: 18px;
			color: #666;
			margin-bottom: 20px;
		}
		
		.section-divider {
			width: 80px;
			height: 4px;
			background: linear-gradient(90deg, #D84727 0%, #c0392b 100%);
			margin: 0 auto;
			border-radius: 2px;
		}

		/* How It Works - Enhanced */
		.how-it-works-enhanced {
			background: #fff;
		}
		
		.step-card-enhanced {
			text-align: center;
			padding: 40px 25px;
			border-radius: 20px;
			background: #fff;
			box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
			transition: all 0.4s ease;
			position: relative;
			height: 100%;
			border: 2px solid transparent;
		}
		
		.step-card-enhanced:hover {
			transform: translateY(-10px);
			box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
			border-color: #D84727;
		}
		
		.step-icon-enhanced {
			width: 90px;
			height: 90px;
			margin: 0 auto 25px;
			background: linear-gradient(135deg, #D84727 0%, #c0392b 100%);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
			font-size: 36px;
			box-shadow: 0 8px 20px rgba(216, 71, 39, 0.3);
		}
		
		.step-number-enhanced {
			position: absolute;
			top: -18px;
			right: -18px;
			width: 50px;
			height: 50px;
			background: #1E3748;
			color: #fff;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 800;
			font-size: 20px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
		}
		
		.step-title-enhanced {
			font-size: 22px;
			font-weight: 700;
			color: #1E3748;
			margin-bottom: 12px;
		}
		
		.step-description-enhanced {
			color: #666;
			font-size: 15px;
			line-height: 1.7;
		}
		
		.step-arrow {
			position: absolute;
			right: -30px;
			top: 50%;
			transform: translateY(-50%);
			color: #D84727;
			font-size: 24px;
			opacity: 0.5;
		}
		
		@media (max-width: 991px) {
			.step-arrow {
				display: none;
			}
		}

		/* Why Choose - Enhanced */
		.why-choose-enhanced {
			background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
		}
		
		.lead-enhanced {
			font-size: 20px;
			color: #555;
			line-height: 1.7;
		}
		
		.feature-list-enhanced {
			margin-top: 30px;
		}
		
		.feature-item-enhanced {
			display: flex;
			gap: 20px;
			margin-bottom: 30px;
			padding: 20px;
			background: #fff;
			border-radius: 12px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
			transition: all 0.3s ease;
		}
		
		.feature-item-enhanced:hover {
			box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
			transform: translateX(5px);
		}
		
		.feature-icon-wrapper {
			width: 50px;
			height: 50px;
			background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
			font-size: 20px;
			flex-shrink: 0;
		}
		
		.feature-title {
			font-weight: 700;
			color: #1E3748;
			margin-bottom: 8px;
			font-size: 18px;
		}
		
		.feature-description {
			color: #666;
			margin: 0;
			line-height: 1.6;
		}
		
		.btn-primary-enhanced {
			background: linear-gradient(135deg, #D84727 0%, #c0392b 100%);
			border: none;
			padding: 14px 30px;
			font-weight: 700;
			border-radius: 12px;
			box-shadow: 0 4px 15px rgba(216, 71, 39, 0.3);
			transition: all 0.3s ease;
		}
		
		.btn-primary-enhanced:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(216, 71, 39, 0.4);
		}
		
		.btn-outline-primary-enhanced {
			border: 2px solid #D84727;
			color: #D84727;
			padding: 14px 30px;
			font-weight: 700;
			border-radius: 12px;
			transition: all 0.3s ease;
		}
		
		.btn-outline-primary-enhanced:hover {
			background: #D84727;
			color: #fff;
			transform: translateY(-2px);
		}

		/* Verification Box - Enhanced */
		.verification-box-enhanced {
			background: #fff;
			padding: 40px;
			border-radius: 20px;
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
		}
		
		.verification-header {
			margin-bottom: 30px;
			padding-bottom: 20px;
			border-bottom: 2px solid #f0f0f0;
		}
		
		.verification-title {
			font-size: 24px;
			font-weight: 700;
			color: #1E3748;
			margin-bottom: 8px;
		}
		
		.verification-subtitle {
			color: #666;
			font-size: 14px;
			margin: 0;
		}
		
		.verification-steps-enhanced {
			margin-top: 20px;
		}
		
		.verification-step-enhanced {
			display: flex;
			gap: 20px;
			margin-bottom: 25px;
			padding-bottom: 25px;
			border-bottom: 1px solid #f0f0f0;
		}
		
		.verification-step-enhanced:last-child {
			border-bottom: none;
			margin-bottom: 0;
			padding-bottom: 0;
		}
		
		.step-icon-box-enhanced {
			width: 60px;
			height: 60px;
			background: linear-gradient(135deg, rgba(216, 71, 39, 0.1) 0%, rgba(216, 71, 39, 0.15) 100%);
			border-radius: 14px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #D84727;
			font-size: 24px;
			flex-shrink: 0;
		}
		
		.step-title-verification {
			font-weight: 700;
			color: #1E3748;
			margin-bottom: 6px;
			font-size: 18px;
		}
		
		.step-description-verification {
			color: #666;
			margin: 0;
			font-size: 14px;
			line-height: 1.6;
		}

		/* Stats Section - New */
		.stats-section {
			background: linear-gradient(135deg, #1E3748 0%, #2c5f7d 100%);
			color: #fff;
		}
		
		.stats-title {
			font-size: 36px;
			font-weight: 700;
			color: #fff;
			margin-bottom: 40px;
		}
		
		.stat-card {
			text-align: center;
			padding: 30px 20px;
			background: rgba(255, 255, 255, 0.1);
			backdrop-filter: blur(10px);
			border-radius: 16px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			transition: all 0.3s ease;
		}
		
		.stat-card:hover {
			background: rgba(255, 255, 255, 0.15);
			transform: translateY(-5px);
		}
		
		.stat-number {
			font-size: 42px;
			font-weight: 800;
			color: #fff;
			margin-bottom: 8px;
		}
		
		.stat-label {
			font-size: 16px;
			color: rgba(255, 255, 255, 0.9);
			font-weight: 600;
		}

		/* CTA Section - Enhanced */
		.cta-section-enhanced {
			background: linear-gradient(135deg, #1E3748 0%, #2c5f7d 50%, #D84727 100%);
			position: relative;
			overflow: hidden;
		}
		
		.cta-section-enhanced::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: url('data:image/svg+xml,<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse"><path d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="60" height="60" fill="url(%23grid)"/></svg>');
			opacity: 0.3;
		}
		
		.cta-title {
			font-size: 38px;
			font-weight: 800;
			color: #fff;
			margin-bottom: 15px;
			position: relative;
			z-index: 1;
		}
		
		.cta-description {
			font-size: 18px;
			color: rgba(255, 255, 255, 0.95);
			position: relative;
			z-index: 1;
		}
		
		.btn-light-enhanced {
			background: #fff;
			color: #1E3748;
			border: none;
			font-weight: 700;
			border-radius: 12px;
			transition: all 0.3s ease;
		}
		
		.btn-light-enhanced:hover {
			background: #f8f9fa;
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
		}
		
		.btn-outline-light-enhanced {
			border: 2px solid #fff;
			color: #fff;
			background: transparent;
			font-weight: 700;
			border-radius: 12px;
			transition: all 0.3s ease;
		}
		
		.btn-outline-light-enhanced:hover {
			background: #fff;
			color: #1E3748;
			transform: translateY(-2px);
		}

		/* Responsive Design */
		@media (max-width: 768px) {
			.hero-title-client {
				font-size: 36px;
			}
			
			.hero-description-client {
				font-size: 16px;
			}
			
			.search-box-enhanced {
				padding: 25px 20px;
			}
			
			.section-title-enhanced {
				font-size: 32px;
			}
			
			.trust-badges-enhanced {
				flex-direction: column;
				align-items: center;
			}
			
			.trust-badge-enhanced {
				width: 100%;
				max-width: 300px;
			}
			
			.step-card-enhanced {
				margin-bottom: 30px;
			}
			
			.cta-title {
				font-size: 28px;
			}
			
			.stat-number {
				font-size: 32px;
			}
		}
		
		/* Accessibility Improvements */
		.btn-search-enhanced:focus,
		.form-control-enhanced:focus,
		.location-tag-enhanced:focus {
			outline: 3px solid rgba(216, 71, 39, 0.5);
			outline-offset: 2px;
		}
		
		/* Smooth Scrolling */
		html {
			scroll-behavior: smooth;
		}
	</style>

	{{-- Google Maps Autocomplete for Location --}}
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			if (typeof google !== 'undefined' && google.maps) {
				const addressInput = document.getElementById('address-client');
				if (addressInput) {
					const autocomplete = new google.maps.places.Autocomplete(addressInput, {
						types: ['geocode'],
						componentRestrictions: { country: 'gb' }
					});
					
					autocomplete.addListener('place_changed', function() {
						const place = autocomplete.getPlace();
						if (place.geometry) {
							document.querySelector('.lat').value = place.geometry.location.lat();
							document.querySelector('.long').value = place.geometry.location.lng();
						}
					});
				}
			}
		});
	</script>
@endsection
