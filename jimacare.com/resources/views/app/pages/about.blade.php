@extends('app.template.layout')

@section('content')
	<div class="about-banner about-pagebanner">
		{{--<div class="container">
			<div class="row">
				<div class="col-md-7">
					<h3 class="mt-3 pb-5">Every day we work to make the world a little better</h3>
				</div>
			</div>
		</div>--}}
		<div class="banner-img">
			<img class="img-responsive" src="{{ asset('img/aboutbanner.png') }}" alt="">
		</div>
	</div>

	<div class="about-content">
		<div class="container">
			<div class="row mt-5 pt-4 mb-5 pb-4">

				<div class="col-lg-5 col-md-6 col-sm-12 col-12">
					<h3>About Us</h3>
					<p>Jimacare was launched in 2020 while considering the importance of quality care for family, affordable care cost and job creation.</p>
					<div class="about-img1">
						<img src="{{ asset('img/about-1.png') }}" alt="">
					</div>
				</div>
				<div class="col-lg-7 col-md-6 col-sm-12 col-12">

					<p class="pt-5 mt-3">Jimacare helps Care homes and families to find Carer and housekeeper while creating employment opportunities for Carers and housekeepers.</p>
					<img class="about-img2" src="{{ asset('img/about-2.png') }}" alt="">
				</div>
				<div class="col-md-1 col-sm-1 col-12">
				</div>
			</div>
		</div>
		<div class="about-numbers">
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-12">
						<ul>
							<li class="border-bottom">
								<span>01</span>
								<h3>Support</h3>
								<p>
									We ensure our platform guarantees a solution in finding and managing family care needs
									which includes elderly Care, Housekeeping, and Child Care.<br/>
								</p>
							</li>
							<li class="border-bottom">
								<span>02</span>
								<h3>Security</h3>
								<p>
									Jimacare takes safety of their clients seriously by ensuring all carers registered on the platform
									are interviewed and must provide references.
								</p>
							</li>
							<li class="border-bottom">
								<span>03</span>
								<h3>Transparency</h3>
								<p>
									To ensure Transparent and quality services, rating of carers are put into consideration for
									selection purpose.
								</p>
							</li>
							<li class="border-bottom">
								<span>04</span>
								<h3>Accountability</h3>
								<p>
									We ensure carers document and training are up to date to effective and efficient care services.
								</p>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
