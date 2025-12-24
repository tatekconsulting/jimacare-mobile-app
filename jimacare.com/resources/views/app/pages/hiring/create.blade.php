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

	<div class="invoice-detail job-detail hairing">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-sm-12 col-12">
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
					<div class="personal-detail p-0">
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
					<div class="care-likes">
						<h2>Specific care requirements, likes, dislikes or instructions</h2>
						<ul>
							<li>- help required 7 nights per week 9:00pm till 7.00 am dressing/undressing.</li>
							<li>- Uses urine bottle twice during night.. general supervision. - Please check location
								using post code in Google Maps.
							</li>
						</ul>
						<a href="#" class="primary-bgclr">Edit</a>
					</div>
				</div>
				<div class="col-md-6 col-sm-12 col-12">
					<div class="my-jobs">
						<h3 class="mb-0">Applications submitted</h3>
						<ul>
							<li>
								<div class="row mb-2">
									<div class="col-12">
										<div class="d-flex">
											<a href="#">Dana Underwood</a>
											<span>/ Experience - 11 Yrs</span>
										</div>
									</div>
								</div>
								<a href="#">Edit</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
