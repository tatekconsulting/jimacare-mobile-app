@extends('app.template.layout')

@section('content')
	<div class="banner">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-5">
							<h3 class="mt-5 pb-4">Jobs details</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="invoice-detail job-detail">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-12">
					<h1>Flat B, 94 Middelton Road, 94 Middelton Road, London , E8, 4NL, UK</h1>
					<div class="job-location">
						<p>Overnight</p>
						<div class="d-flex">
							<div class="">
								<span>09:00 PM</span>
								<p>Start Time</p>
							</div>
							<div class="text-center">
								<span>18:00 AM</span>
								<p>End Time</p>
							</div>
							<div class="">
								<span class="mt-4 d-block text-right">Norwich City</span>
							</div>
						</div>
					</div>
					<div class="care-likes">
						<h2>Specific care requirements, likes, dislikes or instructions</h2>
						<ul>
							<li>- help required 7 nights per week 9&#039;00pm till 7.00 am dressing/undressing.</li>
							<li>- Uses urine bottle twice during night.. general supervision. - Please check location
								using post code in Google Maps.
							</li>
						</ul>
						<a href="#">Apply for job</a>
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-12">
					<div class="personal-detail">
						<h3>Details of person requiring care</h3>
						<ul>
							<li>
								<p>Full name</p>
								<span>Donald Waret</span>
							</li>
							<li>
								<p>Age</p>
								<span>65</span>
							</li>
							<li>
								<p>Postcode</p>
								<span>NR15</span>
							</li>
						</ul>
						<h3>Details of person requiring care</h3>
						<ul>
							<li>
								<p>Care Type</p>
								<span>-</span>
							</li>
							<li>
								<p>Starts When</p>
								<span>Not Sure</span>
							</li>
						</ul>
						<h3>Support needed for</h3>
						<ul>
							<li>
								<p>Dressing / undressing</p>
							</li>
							<li>
								<p>Moving and handling</p>
							</li>
							<li>
								<p>Continence care</p>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-7 col-sm-8 col-12">
					<label>Send a request to the client for the posted vacancy</label>
					<textarea placeholder="With here"></textarea>
					<button>Send</button>
				</div>
			</div>
		</div>
	</div>
@endsection
