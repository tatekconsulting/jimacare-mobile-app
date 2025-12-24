@extends('app.template.layout')

@section('content')
	<div class="about-banner">
		<div class="container">
			<div class="row">
				<div class="col-md-7">
					<h3 class="mt-5 mb-3">Our leadership team</h3>
				</div>
			</div>
		</div>
		<div class="banner-img">
			<img class="img-responsive" src="{{ asset('img/aboutbanner.png') }}" alt="">
		</div>
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-12 col-sm-12 col-12">
					<p class="mt-5">The global care economy is being driven by sweeping demographic shifts and in the coming decades care jobs are projected to be one of the fastest growing job sectors in the world.</p>
				</div>
				<div class="col-md-6 col-sm-6 col-12"></div>
			</div>
		</div>
	</div>

	<div class="our-team">
		<div class="container">
			<div class="row">
				<div class="col-md-2 col-sm-2 col-12">
				</div>
				<div class="col-md-10 col-sm-10 col-12">
					<ul class="owl-carousel team-carousel">
						<li>
							<img src="{{ asset('img/profile.png') }}" alt="">
							<div class="member-content">
								<h3>{{--Tessa Thomson--}}</h3>
								<p>Founder and mastermind of JimaCare</p>
							</div>
						</li>
						<li>
							<img src="{{ asset('img/profile.png') }}" alt="">
							<div class="member-content">
								<h3>{{--Tessa Thomson--}}</h3>
								<p>Founder and mastermind of JimaCare</p>
							</div>
						</li>
						<li>
							<img src="{{ asset('img/profile.png') }}" alt="">
							<div class="member-content">
								<h3>{{--Tessa Thomson--}}</h3>
								<p>Founder and mastermind of JimaCare</p>
							</div>
						</li>
						<li>
							<img src="{{ asset('img/profile.png') }}" alt="">
							<div class="member-content">
								<h3>{{--Tessa Thomson--}}</h3>
								<p>Founder and mastermind of JimaCare</p>
							</div>
						</li>
						<li>
							<img src="{{ asset('img/profile.png') }}" alt="">
							<div class="member-content">
								<h3>{{--Tessa Thomson--}}</h3>
								<p>Founder and mastermind of JimaCare</p>
							</div>
						</li>
						<li>
							<img src="{{ asset('img/profile.png') }}" alt="">
							<div class="member-content">
								<h3>{{--Tessa Thomson--}}</h3>
								<p>Founder and mastermind of JimaCare</p>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@endsection
