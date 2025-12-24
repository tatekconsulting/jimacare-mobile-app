@extends('app.template.layout')

@section('content')
	{{-- Hero Section with Enhanced Search --}}
	<div class="hero-section">
		<div class="hero-overlay"></div>
		<div class="container">
			<div class="row align-items-center min-vh-75">
				<div class="col-12 col-lg-8 mx-auto text-center">
					<div class="hero-content">
						@php
							$showSellersSearch = true;
							if(auth()->check()) {
								$userRole = auth()->user()->role->slug ?? '';
								$isAdmin = auth()->user()->role_id == 1 || $userRole === 'admin';
								$isClient = $userRole === 'client';
								$showSellersSearch = $isAdmin || $isClient;
							}
						@endphp
						
						@if($showSellersSearch)
							<h3 class="hero-subtitle">Give your loved one the Best Care</h3>
							<h1 class="hero-title">UK's Most Trusted, Reliable Carers, Childminders & Housekeepers Near You</h1>
							<p class="hero-description">Find qualified, verified care professionals in your area. Trusted by thousands of families across the UK.</p>
							
							{{-- Enhanced Search Form --}}
							<div class="search-box-modern">
								<form action="{{ route('sellers') }}" method="GET" class="search-form">
								<div class="row g-2">
									<div class="col-12 col-md-4">
										<div class="form-group-modern">
											<label class="form-label-modern">
												<i class="fa fa-user-md"></i> I'm Looking For
											</label>
											<select name="type" class="form-control-modern" required>
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
										<div class="form-group-modern">
											<label class="form-label-modern">
												<i class="fa fa-map-marker-alt"></i> Location
											</label>
											<input type="text" name="address" id="address" 
												   class="form-control-modern address" 
												   placeholder="Enter postcode or town" 
												   required>
											<input type="hidden" name="lat" class="lat" value="{{ request('lat') }}"/>
											<input type="hidden" name="long" class="long" value="{{ request('long') }}"/>
										</div>
									</div>
									<div class="col-12 col-md-3">
										<button type="submit" class="btn-search-modern">
											<i class="fa fa-search"></i> Search Now
										</button>
									</div>
								</div>
							</form>
							
							@if($showSellersSearch)
								{{-- Popular Locations --}}
								<div class="popular-locations">
									<small class="text-white-50 d-block mb-2">Popular Locations:</small>
									<div class="location-tags">
										<a href="{{route('sellers')}}?type=3&address=London%2C+UK&lat=51.5073509&long=-0.1277583" 
										   class="location-tag">Carers in London</a>
										<a href="{{route('sellers')}}?type=4&address=London%2C+UK&lat=51.5073509&long=-0.1277583" 
										   class="location-tag">Childminders in London</a>
										<a href="{{route('sellers')}}?type=5&address=London%2C+UK&lat=51.5073509&long=-0.1277583" 
										   class="location-tag">Housekeepers in London</a>
									</div>
								</div>
							@endif
						</div>
						@else
							<h3 class="hero-subtitle">Welcome to JimaCare</h3>
							<h1 class="hero-title">Find Your Perfect Care Opportunity</h1>
							<p class="hero-description">Browse available jobs and connect with clients who need your services. Join thousands of care professionals across the UK.</p>
							<div class="mt-4">
								<a href="{{ route('contract.index') }}" class="btn-search-modern" style="display: inline-block; text-decoration: none;">
									<i class="fa fa-briefcase mr-2"></i> Browse Available Jobs
								</a>
							</div>
						@endif
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
							<i class="fa fa-shield-alt"></i>
							<span>Enhanced DBS Checked</span>
						</div>
						<div class="trust-badge-item cqc-badge">
							<div class="cqc-logo-container">
								@php
									$cqcLogoPath = 'img/cqc-logo.png';
									$cqcLogoExists = file_exists(public_path($cqcLogoPath));
								@endphp
								@if($cqcLogoExists)
									<img src="{{ asset($cqcLogoPath) }}" 
										 alt="CQC Registered" 
										 class="cqc-logo"
									/>
								@else
									<i class="fa fa-certificate cqc-fallback"></i>
								@endif
							</div>
							<span>CQC Registered</span>
						</div>
						<div class="trust-badge-item">
							<i class="fa fa-user-check"></i>
							<span>Verified Professionals</span>
						</div>
						<div class="trust-badge-item">
							<i class="fa fa-star"></i>
							<span>5-Star Rated Service</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- How It Works Section --}}
	<div class="how-it-works-section py-5">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center mb-5">
					<h2 class="section-title">How It Works</h2>
					<p class="section-subtitle">Get started in 4 simple steps</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card">
						<div class="step-icon">
							<i class="fa fa-user-plus"></i>
						</div>
						<div class="step-number">1</div>
						<h4 class="step-title">Sign Up</h4>
						<p class="step-description">Create your free account in minutes. Choose to be a client or service provider.</p>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card">
						<div class="step-icon">
							<i class="fa fa-search"></i>
						</div>
						<div class="step-number">2</div>
						<h4 class="step-title">Search & Connect</h4>
						<p class="step-description">Post a job or browse profiles. Message candidates who match your needs.</p>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card">
						<div class="step-icon">
							<i class="fa fa-handshake"></i>
						</div>
						<div class="step-number">3</div>
						<h4 class="step-title">Choose Provider</h4>
						<p class="step-description">Review profiles, check references, and select the perfect match for you.</p>
					</div>
				</div>
				<div class="col-md-6 col-lg-3 mb-4">
					<div class="step-card">
						<div class="step-icon">
							<i class="fa fa-check-circle"></i>
						</div>
						<div class="step-number">4</div>
						<h4 class="step-title">Get Started</h4>
						<p class="step-description">Chat with your provider, finalize details, and begin your care journey.</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Why Choose Us Section --}}
	<div class="why-choose-section py-5 bg-light">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-6 mb-4 mb-lg-0">
					<h2 class="section-title">Why Choose JimaCare?</h2>
					<p class="lead">We make it easy to find experienced, trustworthy care professionals who match your specific needs.</p>
					<div class="feature-list">
						<div class="feature-item">
							<i class="fa fa-check-circle text-success"></i>
							<div>
								<h5>Flexible Approach, Stable Results</h5>
								<p>Whether you need someone who speaks a foreign language, drives, or has specialist skills, we help you find the perfect match.</p>
							</div>
						</div>
						<div class="feature-item">
							<i class="fa fa-check-circle text-success"></i>
							<div>
								<h5>Comprehensive Verification</h5>
								<p>All providers undergo thorough background checks, interviews, and identity verification for your peace of mind.</p>
							</div>
						</div>
					</div>
					<a href="{{ route('contract.create') }}" class="btn btn-primary btn-lg mt-3">
						Post a Job <i class="fa fa-arrow-right ml-2"></i>
					</a>
				</div>
				<div class="col-12 col-lg-6">
					<div class="verification-box">
						<h4 class="mb-4">Our Verification Process</h4>
						<div class="verification-steps">
							<div class="verification-step">
								<div class="step-icon-box">
									<i class="fa fa-file-alt"></i>
								</div>
								<div class="step-content">
									<h5>Reference Check</h5>
									<p>We confirm suitability through previous employer references.</p>
								</div>
							</div>
							<div class="verification-step">
								<div class="step-icon-box">
									<i class="fa fa-comments"></i>
								</div>
								<div class="step-content">
									<h5>Interview Process</h5>
									<p>Every provider is interviewed to assess skills, experience, and reliability.</p>
								</div>
							</div>
							<div class="verification-step">
								<div class="step-icon-box">
									<i class="fa fa-shield-alt"></i>
								</div>
								<div class="step-content">
									<h5>Enhanced DBS Check</h5>
									<p>Full background checks guarantee trust and safety for your loved ones.</p>
								</div>
							</div>
							<div class="verification-step">
								<div class="step-icon-box">
									<i class="fa fa-id-card"></i>
								</div>
								<div class="step-content">
									<h5>Identity Verification</h5>
									<p>Facial recognition and document verification ensure authenticity.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Testimonials Section --}}
	<div class="testimonials-section py-5">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center mb-5">
					<h2 class="section-title">What Our Clients Say</h2>
					<p class="section-subtitle">Real feedback from families we've helped</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-4">
					<div class="testimonial-card">
						<div class="testimonial-header">
							<img src="{{ asset('img/jhon-scott.jpg') }}" alt="John Scott" class="testimonial-avatar">
							<div class="testimonial-info">
								<h5>John Scott</h5>
								<p class="text-muted mb-0">London</p>
								<div class="testimonial-rating">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
								</div>
							</div>
							<span class="testimonial-date">12 October 2020</span>
						</div>
						<div class="testimonial-body">
							<p>"I was searching online and stumbled at Jimacare.com. I reached out to customer service team and a professional and friendly carer was sent to my house immediately. The price fits in to my plan to hire a carer for my wife. I'm so happy for getting an outstanding carer on this platform."</p>
						</div>
					</div>
				</div>
				<div class="col-md-6 mb-4">
					<div class="testimonial-card">
						<div class="testimonial-header">
							<img src="{{ asset('img/client-img.png') }}" alt="Sarah Thompson" class="testimonial-avatar">
							<div class="testimonial-info">
								<h5>Sarah Thompson</h5>
								<p class="text-muted mb-0">London</p>
								<div class="testimonial-rating">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
								</div>
							</div>
							<span class="testimonial-date">21 February 2021</span>
						</div>
						<div class="testimonial-body">
							<p>"I called Jimacare support team to get a Housekeeper to clean my house. I was so happy with the friendly support given. I'm so fascinated to have hired a professional cleaner through Jimacare. She understood what's required of her. She ensured all safety measures are put in place. I am satisfied with her work and I will hire her again through this website."</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- FAQ Section --}}
	<div class="faq-section py-5 bg-light">
		<div class="container">
			<div class="row">
				<div class="col-12 col-lg-8 mx-auto">
					<div class="text-center mb-5">
						<h2 class="section-title">Frequently Asked Questions</h2>
						<p class="section-subtitle">Everything you need to know</p>
					</div>
					<div class="faq-accordion">
						<div class="faq-item">
							<div class="faq-question">
								<h5>What service does JimaCare provide?</h5>
								<i class="fa fa-chevron-down"></i>
							</div>
							<div class="faq-answer">
								<p>JimaCare is a CQC registered Care service provider. We are here to provide a professional service that clients will be happy with.</p>
							</div>
						</div>
						<div class="faq-item">
							<div class="faq-question">
								<h5>Who can see my client profile?</h5>
								<i class="fa fa-chevron-down"></i>
							</div>
							<div class="faq-answer">
								<p>Only the Admin team can see your client profile. Your privacy is our priority.</p>
							</div>
						</div>
						<div class="faq-item">
							<div class="faq-question">
								<h5>Are checks carried out on the Carers, Childminders and Housekeepers?</h5>
								<i class="fa fa-chevron-down"></i>
							</div>
							<div class="faq-answer">
								<p>All Carers, Childminders and Housekeepers undergo criminal record checks and are interviewed. They have legal right to work in United Kingdom and are tax compliant.</p>
							</div>
						</div>
					</div>
					<div class="text-center mt-4">
						<a href="{{route('helpdesk')}}" class="btn btn-outline-primary btn-lg">
							Visit Helpdesk for More <i class="fa fa-arrow-right ml-2"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Blog Section --}}
	@if($posts && $posts->count() > 0)
	<div class="blog-section py-5">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center mb-5">
					<h2 class="section-title">Latest Blog News</h2>
					<p class="section-subtitle">Stay informed with our latest articles</p>
				</div>
			</div>
			<div class="row">
				@foreach($posts as $post)
					<div class="col-md-6 col-lg-4 mb-4">
						<div class="blog-card">
							<a href="{{ route('post', ['post' => $post->id]) }}">
								<div class="blog-image">
									<img src="{{ asset($post->image) }}" alt="{{ $post->title }}" class="img-fluid">
									<div class="blog-overlay"></div>
								</div>
							</a>
							<div class="blog-content">
								<h4 class="blog-title">
									<a href="{{ route('post', ['post' => $post->id]) }}">{{ $post->title }}</a>
								</h4>
								<a href="{{ route('post', ['post' => $post->id]) }}" class="blog-read-more">
									Read More <i class="fa fa-arrow-right ml-2"></i>
								</a>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
	@endif

	{{-- CTA Section --}}
	<div class="cta-section py-5 bg-primary text-white">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-8 text-center text-lg-left mb-4 mb-lg-0">
					<h2 class="mb-2">Want to Join Our Platform?</h2>
					<p class="lead mb-0">Join thousands of care professionals and clients across the UK</p>
				</div>
				<div class="col-12 col-lg-4">
					<div class="cta-buttons">
						<a href="{{ route('register.type', ['type' => 'carer']) }}" class="btn btn-light btn-block mb-2">
							<i class="fa fa-user-md mr-2"></i> Sign Up as Carer
						</a>
						<a href="{{ route('register.type', ['type' => 'housekeeper']) }}" class="btn btn-light btn-block mb-2">
							<i class="fa fa-broom mr-2"></i> Sign Up as Housekeeper
						</a>
						<a href="{{ route('register.type', ['type' => 'childminder']) }}" class="btn btn-light btn-block mb-2">
							<i class="fa fa-child mr-2"></i> Sign Up as Childminder
						</a>
						<a href="{{ route('register.type', ['type' => 'client']) }}" class="btn btn-outline-light btn-block">
							<i class="fa fa-user mr-2"></i> Sign Up as Client
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Custom Styles --}}
	<style>
		/* Hero Section */
		.hero-section {
			position: relative;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			background-image: url('{{ asset("img/slider-banner.png") }}');
			background-size: cover;
			background-position: center;
			padding: 100px 0;
			min-height: 600px;
			display: flex;
			align-items: center;
		}
		.hero-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: linear-gradient(135deg, rgba(30, 55, 72, 0.85) 0%, rgba(216, 71, 39, 0.75) 100%);
		}
		.hero-content {
			position: relative;
			z-index: 2;
		}
		.hero-subtitle {
			color: #fff;
			font-size: 18px;
			font-weight: 400;
			margin-bottom: 15px;
			opacity: 0.9;
		}
		.hero-title {
			color: #fff;
			font-size: 48px;
			font-weight: 700;
			line-height: 1.2;
			margin-bottom: 20px;
		}
		.hero-description {
			color: rgba(255, 255, 255, 0.9);
			font-size: 18px;
			margin-bottom: 40px;
		}
		.min-vh-75 {
			min-height: 75vh;
		}

		/* Search Box Modern */
		.search-box-modern {
			background: rgba(255, 255, 255, 0.95);
			border-radius: 20px;
			padding: 30px;
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
			margin-top: 30px;
		}
		.form-group-modern {
			margin-bottom: 0;
		}
		.form-label-modern {
			display: block;
			font-size: 12px;
			font-weight: 600;
			color: #1E3748;
			margin-bottom: 8px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		.form-label-modern i {
			margin-right: 5px;
			color: #D84727;
		}
		.form-control-modern {
			width: 100%;
			padding: 12px 15px;
			border: 2px solid #e0e0e0;
			border-radius: 10px;
			font-size: 16px;
			transition: all 0.3s ease;
		}
		.form-control-modern:focus {
			border-color: #D84727;
			outline: none;
			box-shadow: 0 0 0 3px rgba(216, 71, 39, 0.1);
		}
		.btn-search-modern {
			width: 100%;
			padding: 12px 20px;
			background: linear-gradient(135deg, #D84727 0%, #c0392b 100%);
			color: #fff;
			border: none;
			border-radius: 10px;
			font-size: 16px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
			margin-top: 28px;
		}
		.btn-search-modern:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(216, 71, 39, 0.4);
		}
		.popular-locations {
			margin-top: 20px;
			padding-top: 20px;
			border-top: 1px solid rgba(0, 0, 0, 0.1);
		}
		.location-tags {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
		}
		.location-tag {
			display: inline-block;
			padding: 6px 15px;
			background: rgba(216, 71, 39, 0.1);
			color: #D84727;
			border-radius: 20px;
			font-size: 13px;
			text-decoration: none;
			transition: all 0.3s ease;
		}
		.location-tag:hover {
			background: #D84727;
			color: #fff;
			text-decoration: none;
		}

		/* Trust Section */
		.trust-section {
			background: #fff;
			padding: 30px 0;
			box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.05);
		}
		.trust-badges {
			display: flex;
			justify-content: space-around;
			flex-wrap: wrap;
			gap: 20px;
		}
		.trust-badge-item {
			display: flex;
			align-items: center;
			gap: 10px;
			font-weight: 600;
			color: #1E3748;
		}
		.trust-badge-item i {
			font-size: 24px;
			color: #D84727;
		}
		.cqc-badge {
			position: relative;
		}
		.cqc-logo-container {
			display: flex;
			align-items: center;
			justify-content: center;
			min-width: 40px;
			height: 45px;
		}
		.cqc-logo {
			height: 45px;
			width: auto;
			max-width: 140px;
			object-fit: contain;
			display: none;
		}
		.cqc-fallback {
			display: inline-block;
			font-size: 24px;
			color: #D84727;
		}
		@media (max-width: 768px) {
			.cqc-logo-container {
				height: 35px;
			}
			.cqc-logo {
				height: 35px;
				max-width: 100px;
			}
		}

		/* Section Titles */
		.section-title {
			font-size: 42px;
			font-weight: 700;
			color: #1E3748;
			margin-bottom: 15px;
		}
		.section-subtitle {
			font-size: 18px;
			color: #666;
			margin-bottom: 0;
		}

		/* How It Works */
		.how-it-works-section {
			background: #fff;
		}
		.step-card {
			text-align: center;
			padding: 30px 20px;
			border-radius: 15px;
			background: #fff;
			box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
			transition: all 0.3s ease;
			position: relative;
			height: 100%;
		}
		.step-card:hover {
			transform: translateY(-10px);
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
		}
		.step-icon {
			width: 80px;
			height: 80px;
			margin: 0 auto 20px;
			background: linear-gradient(135deg, #D84727 0%, #c0392b 100%);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
			font-size: 32px;
		}
		.step-number {
			position: absolute;
			top: -15px;
			right: -15px;
			width: 40px;
			height: 40px;
			background: #1E3748;
			color: #fff;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 700;
			font-size: 18px;
		}
		.step-title {
			font-size: 20px;
			font-weight: 600;
			color: #1E3748;
			margin-bottom: 10px;
		}
		.step-description {
			color: #666;
			font-size: 14px;
			line-height: 1.6;
		}

		/* Why Choose Section */
		.feature-list {
			margin-top: 30px;
		}
		.feature-item {
			display: flex;
			gap: 15px;
			margin-bottom: 25px;
		}
		.feature-item i {
			font-size: 24px;
			margin-top: 5px;
		}
		.feature-item h5 {
			font-weight: 600;
			color: #1E3748;
			margin-bottom: 5px;
		}
		.feature-item p {
			color: #666;
			margin: 0;
		}
		.verification-box {
			background: #fff;
			padding: 30px;
			border-radius: 15px;
			box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
		}
		.verification-steps {
			margin-top: 20px;
		}
		.verification-step {
			display: flex;
			gap: 20px;
			margin-bottom: 25px;
			padding-bottom: 25px;
			border-bottom: 1px solid #e0e0e0;
		}
		.verification-step:last-child {
			border-bottom: none;
			margin-bottom: 0;
			padding-bottom: 0;
		}
		.step-icon-box {
			width: 50px;
			height: 50px;
			background: rgba(216, 71, 39, 0.1);
			border-radius: 10px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #D84727;
			font-size: 20px;
			flex-shrink: 0;
		}
		.step-content h5 {
			font-weight: 600;
			color: #1E3748;
			margin-bottom: 5px;
		}
		.step-content p {
			color: #666;
			margin: 0;
			font-size: 14px;
		}

		/* Testimonials */
		.testimonials-section {
			background: #f8f9fa;
		}
		.testimonial-card {
			background: #fff;
			padding: 30px;
			border-radius: 15px;
			box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
			height: 100%;
		}
		.testimonial-header {
			display: flex;
			align-items: center;
			gap: 15px;
			margin-bottom: 20px;
			padding-bottom: 20px;
			border-bottom: 1px solid #e0e0e0;
		}
		.testimonial-avatar {
			width: 60px;
			height: 60px;
			border-radius: 50%;
			object-fit: cover;
		}
		.testimonial-info {
			flex: 1;
		}
		.testimonial-info h5 {
			margin: 0;
			font-weight: 600;
			color: #1E3748;
		}
		.testimonial-rating {
			color: #ffc107;
			margin-top: 5px;
		}
		.testimonial-date {
			font-size: 12px;
			color: #999;
		}
		.testimonial-body p {
			color: #666;
			line-height: 1.8;
			margin: 0;
		}

		/* FAQ */
		.faq-accordion {
			margin-top: 30px;
		}
		.faq-item {
			background: #fff;
			border-radius: 10px;
			margin-bottom: 15px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
			overflow: hidden;
		}
		.faq-question {
			padding: 20px;
			cursor: pointer;
			display: flex;
			justify-content: space-between;
			align-items: center;
			transition: all 0.3s ease;
		}
		.faq-question:hover {
			background: #f8f9fa;
		}
		.faq-question h5 {
			margin: 0;
			font-weight: 600;
			color: #1E3748;
		}
		.faq-question i {
			color: #D84727;
			transition: transform 0.3s ease;
		}
		.faq-item.active .faq-question i {
			transform: rotate(180deg);
		}
		.faq-answer {
			max-height: 0;
			overflow: hidden;
			transition: max-height 0.3s ease;
		}
		.faq-item.active .faq-answer {
			max-height: 500px;
		}
		.faq-answer p {
			padding: 0 20px 20px;
			color: #666;
			margin: 0;
		}

		/* Blog Section */
		.blog-section {
			background: #fff;
		}
		.blog-card {
			background: #fff;
			border-radius: 15px;
			overflow: hidden;
			box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
			transition: all 0.3s ease;
			height: 100%;
			display: flex;
			flex-direction: column;
		}
		.blog-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
		}
		.blog-image {
			position: relative;
			overflow: hidden;
			height: 200px;
		}
		.blog-image img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			transition: transform 0.3s ease;
		}
		.blog-card:hover .blog-image img {
			transform: scale(1.1);
		}
		.blog-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.3) 100%);
		}
		.blog-content {
			padding: 20px;
			flex: 1;
			display: flex;
			flex-direction: column;
		}
		.blog-title {
			font-size: 20px;
			font-weight: 600;
			color: #1E3748;
			margin-bottom: 15px;
		}
		.blog-title a {
			color: #1E3748;
			text-decoration: none;
		}
		.blog-title a:hover {
			color: #D84727;
		}
		.blog-read-more {
			color: #D84727;
			text-decoration: none;
			font-weight: 600;
			margin-top: auto;
		}
		.blog-read-more:hover {
			text-decoration: underline;
		}

		/* CTA Section */
		.cta-section {
			background: linear-gradient(135deg, #1E3748 0%, #2c5f7d 100%);
		}
		.cta-buttons .btn {
			transition: all 0.3s ease;
		}
		.cta-buttons .btn:hover {
			transform: translateX(5px);
		}

		/* Responsive */
		@media (max-width: 768px) {
			.hero-title {
				font-size: 32px;
			}
			.hero-description {
				font-size: 16px;
			}
			.search-box-modern {
				padding: 20px;
			}
			.section-title {
				font-size: 32px;
			}
			.trust-badges {
				flex-direction: column;
				align-items: center;
			}
		}
	</style>

	{{-- FAQ Accordion JavaScript --}}
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const faqItems = document.querySelectorAll('.faq-item');
			faqItems.forEach(item => {
				const question = item.querySelector('.faq-question');
				question.addEventListener('click', () => {
					// Close other items
					faqItems.forEach(otherItem => {
						if (otherItem !== item) {
							otherItem.classList.remove('active');
						}
					});
					// Toggle current item
					item.classList.toggle('active');
				});
			});
		});
	</script>
@endsection

