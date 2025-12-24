@extends('app.template.layout')

@section('content')
	<div class="banner">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-5">
							<h3 class="mt-5 pb-4">Shift Invoice Details</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="invoice-detail">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-12">
					<h1>Flat B, 94 Middelton Road, 94 Middelton Road, London , E8, 4NL, UK</h1>
					<h2><span>Shift Time:</span> LiveIn</h2>
					<h2>Shift Details</h2>
					<ul>
						<li>
							<p>Start Date/Time</p>
							<span>09 Sep 2020, 06:30 AM</span>
						</li>
						<li>
							<p>End Date/Time</p>
							<span>09 Sep 2020, 07:30 AM</span>
						</li>
						<li>
							<p>Care Duration</p>
							<span>1 hr, 00 mins</span>
						</li>
						<li>
							<p>Shift Fees</p>
							<span>$ 21.00</span>
						</li>
					</ul>
				</div>
				<div class="col-md-6 col-sm-6 col-12">
					<div class="pending-approval">
						<div class="blue-box">
							<span>Your total earnings after deducation of JimaCare commisions of 12,5 %</span>
							<h4>$ 87.5</h4>
						</div>
						<p class="mt-0">Please approve the shift and complete the payment to confirm the shift</p>
						<div>
							<a href="#" class="three-bg">Submit for approval</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
