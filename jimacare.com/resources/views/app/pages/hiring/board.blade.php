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
					<h1>Dana Underwood <span> / Experience - 11 Yrs</span></h1>
					<p>I have been a live-in carer since 2007. The flexibility of work dates tied in well with my four children's school…</p>
					<div class="job-location hire-location pr-0">
						<p>$ 750 Live-in/Week</p>
						<p class="d-flex">$ 120 Live-in/Day <a href="#" class="ml-auto">View profile</a></p>
					</div>
				</div>
				<div class="col-lg-6 col-md-12 col-sm-12 col-12">
					<div class="hire-btns">
						<a href="#">Hire a Service</a>
						<a href="#">Reject</a>
					</div>
				</div>
			</div>
		</div>
		<div class="chat-board">
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-12">
						<h3>Description of the proposed job</h3>
						<p>Good afternoon, I will be glad to help you, I ask you to consider my candidacy for the vacancy and get acquainted with my work experience.</p>
						<h3>Messages: Dana Underwood</h3>
						<div class="chat-box border">
							<ul>
								<li>
									<p>Dana Underwood</p>
									<span>Good afternoon, I will be glad to help you</span>
								</li>
								<li>
									<p>Me</p>
									<span>Hello, tell us more about your experience</span>
								</li>
							</ul>
							<div class="chat-text border">
								<div class="d-flex">
									<input type="text" placeholder="Type something.." >
									<img src="{{ asset('img/smile.svg') }}" alt="">
									<button>Send</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
