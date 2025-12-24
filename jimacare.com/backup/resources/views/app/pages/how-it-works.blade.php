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
							<p>Meet carers directly by contacting them to know each other, agree on rates hours and services to be provided.</p>
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
								<h3>Whats the cost of hiring carer on Jimacare Platform?</h3>
								<p>Self -employed carer sets their own rates. Rates can be negotiated with
									carers based on the job preference and number of hours.</p>
							</li>
							<li>
								<h3>How will Carer be paid?</h3>
								<p>The Carer and client must agree on the service to be provided. The
									carer will issue shift invoice as agreed based on fixed time and specific
									amount of money. After the receipt has been received by the client,
									client will make a payment on a secure online payment system. To
									make payment using other option contact support@jimacare.com</p>
							</li>
							<li>
								<h3>How does Jimacare get Commission?</h3>
								<p>Jimacare charges are included in the carer/client agreed rate.</p>
							</li>
							<li>
								<h3>Can you advertise for carer for me and whats the cost?</h3>
								<p>We care advertise your care needs to carers on the platform at no cost.</p>
							</li>
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
							<li>
								<h3>Who makes choice of carer for client?</h3>
								<p>Client makes choice of carer.</p>
							</li>
							<li>
								<h3>How does insurance for carer work?</h3>
								<p>Self-employed carer making transaction through Jimacare platform will be covered by
									self- employed insurance policy.</p>
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
							<p>Book your online interview and go live on the platform</p>
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
								<p>Interview will be conducted by our staff on Video due to Covid-19.</p>
							</li>
							<li>
								<h3>Who pays the JimaCare commission?</h3>
								<p>Jimare care takes 12.1%  (VAT) transaction fee of the agreed amount between clients and service providers.</p>
							</li>

						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
