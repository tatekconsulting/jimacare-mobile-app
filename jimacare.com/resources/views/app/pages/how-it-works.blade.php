@extends('app.template.layout')

@section('content')
	<div class="how-it-works pt-5">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-12">
					<h2>Things to know</h2>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-12">
					<img src="{{ asset('img/work-1.png') }}" alt="">
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-12">

					<h3 class="primary-clr">To find Carers <span>01</span></h3>
					<ul class="carers">
						<li>
							<p>Search carers in close to your local area, join for free.</p>
						</li>
						<li>
							<p>Our online platform handles your payment without aisle.</p>
						</li>
					</ul>
				</div>
			</div>
			<div class="row mt-0 mt-sm-5 ">

				<div class="col-md-4 col-sm-4 col-12">
					<h2>Guide</h2>
				</div>
				<div class="col-md-4 col-sm-4 col-12">
				</div>
				<div class="col-md-12 col-sm-12 col-12">
					<div class="accordian">
						<ul>
							<li>
								<h3>Who can see my client profile?</h3>
								<p>Only the Admin team.</p>
							</li>
							<li>
								<h3>Are checks carried out on the carers?</h3>
								<p>All the carers undergo criminal record checks and are
									interviewed. Carers have legal right to work in United Kingdom
									and are tax compliant.</p>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="container mt-5  how-3">
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-12 col-12">
					<img src="{{ asset('img/work-2.png') }}" alt="">
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-12">

					<h3 class="sec-clr">Where do I start <span>02</span></h3>
					<ul class="carers">
						<li>
							<p>Build your profile so clients can see what a great carer you are</p>
						</li>
						<li>
							<p>Book your online interview</p>
						</li>
					</ul>
				</div>
			</div>
			<div class="row mt-5">

				<div class="col-md-4 col-sm-4 col-12 mt-0 mt-md-5">
					<h2>Guide</h2>
				</div>
				<div class="col-md-4 col-sm-4 col-12">
				</div>
				<div class="col-md-12 col-sm-12 col-12">
					<div class="accordian">
						<ul>
							<li>
								<h3>Where do I start?</h3>
								<p>Please sign up, complete your profile and book an interview</p>
							</li>
							<li>
								<h3>Where is the Interview?</h3>
								<p>Interview will be conducted by our staff.</p>
							</li>

						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
