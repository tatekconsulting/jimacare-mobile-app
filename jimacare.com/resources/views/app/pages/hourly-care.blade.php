@extends('app.template.layout')

@section('content')
	<div class="banner mb-4">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="row">

						<div class="col-md-5">
							<h3 class="mt-5">Hourly Care</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="healthcare-box">
		<div class="row m-0">
			<div class="col-md-5 col-sm-5 col-12"></div>
			<div class="col-md-7 col-sm-7 col-12 pr-0">
				<img src="{{ asset('img/hourly-care.png') }}" alt="" >
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-12">
					<div class="blue-box">
						<h3>Why choose day care/hourly care with JimaCare?</h3>
						<ul>
							<li>
								Companionship
							</li>
							<li>
								Greater security
							</li>
							<li>
								Increased mobility
							</li>
							<li>
								Improved dietary health
							</li>
							<li>
								Assistance with medication
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="about-ques">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-12">
					<h3>Why choose day care/hourly care with Curam?</h3>
					<p class="pl-4 pr-4">The best care comes when we have a positive relationship with those who help us. JimaCare allows you to choose who that person is, making them a consistent and familiar presence in your life.</p>
				</div>
				<div class="col-md-6 col-sm-6 col-12">
					<h3>What can you expect to pay for day care/hourly care?</h3>
					<p class="pl-4 pr-4">With JimaCare, you only pay for the care delivered.
					</p>
					<p class="pl-4 pr-4">
						Depending on the experience of your carer, you can expect to pay from £12 to £16 per hour.
					</p>
					<p class="pl-4 pr-4">
						Prices can be negotiated with the carer and include JimaCare’s fees.
					</p>
				</div>
			</div>
		</div>
	</div>
	<div class="full-width my-5 py-5">
		<div class="container">
			<div class="career-box">
				<p>Help us best match a carer to your needs</p>
				<div class="btn-group">
					<a class="primary-bgclr" href="#">Create</a>
				</div>
			</div>
		</div>
	</div>
@endsection
