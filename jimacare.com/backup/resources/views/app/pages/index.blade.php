@extends('app.template.layout')

@section('content')
	<div class="banner home-banner">
		<div class="container">
			<div class="row">
				<div class="col-12 col-lg-7 col-md-9">
					<form action="{{ route('sellers') }}" method="GET" class="row">
						<div class="col-12">
							<h3 class="font-italic">Give your love one the Best Care</h3>
							<h1 class="text-white">UK most trusted, reliable carer, childminders and housekeepers near you.</h1>
						</div>

						<div class="col-12 mt-3 location-autofill">
							<div class="row no-gutters">
								<div class="col-12 col-md-6 col-lg-4 form-group">
									<select name="type"
											id="" class="custom-select custom-select-lg"
									>
										<option value="">I'm Looking For</option>
										@foreach($roles as $role)
											<option value="{{ $role->id }}"
												@if(request('type') == $role->id) selected @endif
											>{{ $role->title }}</option>
										@endforeach
									</select>
								</div>

								<div class="col-12 col-md-6 col-lg-5 form-group">
									<input type="text" name="address"
										   id="address" class="form-control form-control-lg address"
										   placeholder="Enter postcode or Town"
									>
								</div>
								<input type="hidden" name="lat" class="lat" value="{{ request('lat') }}"/>
								<input type="hidden" name="long" class="long" value="{{ request('long') }}"/>
								<div class="col-12 col-lg-3">
									<button class="btn btn-primary btn-lg btn-block px-3" type="submit"><span class="fa fa-search mr-3"></span> Search</button>
								</div>
							</div>

							<div class="row no-gutter">
								<div class="col-12">
									<button class="border-0 mt-xl-0 mt-lg-0 mt-3 mb-2 text-dark font-weight-bold">Popular Location</button>
									<br>
									<a class="btn btn-sm btn-primary mr-2"
									   href="{{route('sellers')}}?type=3&address=London%2C+UK&lat=51.5073509&long=-0.1277583">Carers in London</a>
									<a class="btn btn-sm btn-primary mr-2"
									   href="{{route('sellers')}}?type=4&address=London%2C+UK&lat=51.5073509&long=-0.1277583">Childminders
										in London</a>
									<a class="btn btn-sm btn-primary" href="{{route('sellers')}}?type=5&address=London%2C+UK&lat=51.5073509&long=-0.1277583">Housekeepers
										in London</a>
								</div>
							</div>

						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="full-width">
		<div class="container">
			<div class="job-box">
				<div class="row">
					<h2 class="pl-3 text-dark">Our process of sourcing Carers, Childminders and Housekeepers</h2>
					<div class="col-12 order-2 order-md-1">
						<h3>Flexible approach, stable results</h3>
						<p>
							We make it very easy for you to select, shortlist, message and hire experienced self-employed Carers, Childminders and Housekeepers.
							Use our search filters to find the ideal service provider whether you require someone who speaks a foreign language, drives or has
							complimentary hobbies or specialist skills
						</p>
						<a class="btn btn-lg btn-success mt-4 px-4" href="{{ route('contract.create') }}">How can we help you? <span
								class="fa fa-long-arrow-right ml-3"></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="full-width approach-boxes">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h3>Trust our providers through our diligent process</h3>
					<ul class="list-unstyled">
						<li class="mb-2">
							<b>✓ Reference Check:</b> We confirm the suitability and effectiveness of the provider through previous employer.
						</li>
						<li class="mb-2">
							<b>✓ Interview Process:</b> We do interview for every new provider to access their level of skills, experience and reliability.
						</li>
						<li class="mb-2">
							<b>✓ Enhanced DBS Check :</b> All Cares, Childminders and Housekeepers undergo full background check to guarantee trust and safety
							of your loved one.
						</li>
						<li class="mb-2">
							<b>✓ Identity Verification with facial recognition and document checks:</b> We use facial recognition and document verification
							software to carry out identity checks on all providers.
						</li>
					</ul>
				</div>
				<h1 class="text-dark text-center my-3 col-12">How it works</h1>
				<div class="row justify-content-center align-content-center text-center">
					<div class="col-md-4">
						<img src="{{asset('img/rocket-icon.png')}}" alt="{{config('app.name')}} rocket-icon" width="64px">
						<p class="font-weight-bold mt-3 mb-0">Sign up</p>
						<p class="text-muted small">Create an account to get started.</p>
					</div>
					<div class="col-md-4">
						<img src="{{asset('img/connect-icon.png')}}" alt="{{config('app.name')}} connect-icon" width="64px">
						<p class="font-weight-bold mt-3 mb-0">Connect</p>
						<p class="text-muted small">Post a job, review profiles, then message candidates who fit your needs.</p>
					</div>
					<div class="col-md-4">
						<img src="{{asset('img/selection-icon.png')}}" alt="{{config('app.name')}} select-icon" width="64px">
						<p class="font-weight-bold mt-3 mb-0">Chose the provider</p>
						<p class="text-muted small">Conduct Interviews, check references, and hire the one that works for you.</p>
					</div>
					<div class="col-md-4">
						<img src="{{asset('img/chat-icon.png')}}" alt="{{config('app.name')}} select-icon" width="64px">
						<p class="font-weight-bold mt-3 mb-0">Chat with provider</p>
						<p class="text-muted small">discuss with provider what you want them to do.</p>
					</div>
					<div class="col-md-4">
						<img src="{{asset('img/payment-icon.png')}}" alt="{{config('app.name')}} select-icon" width="64px">
						<p class="font-weight-bold mt-3 mb-0">Payment, Insurance and further verification</p>
						<p class="text-muted small">Payment, insurance and other checks is done by Admin.</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row py-5 my-5">
			<div class="col-12">
				<div class="help-accordian">
					<div class="container">
						<div class="row">
							<div class="helpdesk-head">
								<h3 class="pl-3">Guide</h3>
							</div>
							<div class="col-md-12 col-sm-12 col-12">
								<ul class="faqs-accordian">
									<li>
										<h3>What service does Jimacare provide?</h3>
										<div>
											Jimacare is an introductory serviceas defined by CQC.We are here to provide platform that helps client and Service
											providers to find each other, provide secure means of managing payment and helps to build trust for future
											engagement.
										</div>
									</li>
									<li>
										<h3>What's the cost of hiring Carer, Childminder or Housekeeper on Jimacare Platform?</h3>
										<div>
											Carer, Childminder and Housekeeper sets their own rates. Rates can be negotiated with carers based on the job
											preference and number of hours.
										</div>
									</li>
									<li>
										<h3>Can you advertise for me and whats the cost?</h3>
										<div>
											We can advertise your needs to Carers, Childminders and Housekeepers on the platform at no cost.
										</div>
									</li>
									<li>
										<h3>Who can see my client profile?</h3>
										<div>
											Only the Admin team.
										</div>
									</li>
									<li>
										<h3>Are checks carried out on the Carers, Childminders and Housekeepers?</h3>
										<div>
											All the Carers, Childmindersand Housekeepers undergo criminal record checks and are interviewed. Carers,
											Childminders and Housekeepers have legal right to work in United Kingdom and are tax compliant.
										</div>
									</li>
									<li>
										<h3>Who makes choice of Carer, Childminders and Housekeeper for client?</h3>
										<div>
											Client makes choice.
										</div>
									</li>
									<li>
										<h3>How does insurance for Carer work?</h3>
										<div>
											Self-employed Carer making transaction through Jimacare platform will be covered by self-employed insurance
											policy. Childminders and Housekeepers provide their own insurance.
										</div>
									</li>
								</ul>
								<div class="text-center">
									<a class="btn btn-lg btn-primary" href="{{route('helpdesk')}}">For more visit our Helpdesk</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="below-pagecontent">
		<div class="our-clients">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h3>What our <br>clients say</h3>
						<ul class="client-carousel owl-carousel">
							<li>
								<div class="bg">
									<div class="row">
										<div class="col-lg-3 col-md-4 col-sm-5 col-12">
											<div class="client-img">
												<img src="{{ asset('img/jhon-scott.jpg') }}" alt="{{config('app.name')}} client photo" style="width:200px;">
												<h4>John Scott</h4>
												<h5>London</h5>
											</div>
										</div>
										<div class="col-lg-9 col-md-8 col-sm-8 col-12">
											<div class="client-content">
												<span>12 OCTOBER 2020</span>
												<p class="text-justify">I was searching online and stumbled at
													Jimacare.com. I reached out to one carer on
													the platform after checking her profile. I had a
													short interview with her. The price fits in to
													my plan to hire a carer for my wife. She was
													friendly and caring. I’ m so happy for getting
													an outstanding carer on this platform.
												</p>
											</div>
										</div>
									</div>
								</div>
							</li>
							<li>
								<div class="bg">
									<div class="row">
										<div class="col-lg-3 col-md-4 col-sm-5 col-12">
											<div class="client-img">
												<img src="{{ asset('img/client-img.png') }}" alt="{{config('app.name')}} client photo" style="width:200px;">
												<h4>Sarah Thompson</h4>
												<h5>London</h5>
											</div>
										</div>
										<div class="col-lg-9 col-md-8 col-sm-8 col-12">
											<div class="client-content">
												<span>21 FEBRUARY 2021</span>
												<p>I called Jimacare support team to get a
													Housekeeper to clean my house. I was so happy
													with the friendly support given. I’ m so fascinated
													to have hired a professional cleaner through
													Jimacare. She understood what’s required of her.
													She ensured all safety measures are put in place.
													I am satisfied with her work and I will hire her
													again through this website.
												</p>
											</div>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="news-stories mt-5">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h2>Blog News</h2>
					</div>
					@foreach($posts as $post)
						<div @if($loop->iteration==1) class="col-md-6 col-sm-12 col-12" @else class="col-md-3 col-sm-12 col-12" @endif>
							<a href="{{ route('post', ['post' => $post->id]) }}">
								<img src="{{ asset($post->image) }}" class="" alt="{{config('app.name')}} post image">
							</a>
							<h3>{{ $post->title }}</h3>
							<div class="readmore"><a href="{{ route('post', ['post' => $post->id]) }}">Read more</a></div>
						</div>
					@endforeach
				</div>
			</div>
		</div>

		<div class="container">
			<div class="career-box">
				<div class="row">
					<div class="col-12 text-center py-2">
						Want to join our platform?
					</div>
					<div class="col-12 btn-group-lg text-center">
						<a href="{{ route('register.type' , [ 'type' => 'carer' ]) }}" class="btn btn-success">Signup as a Carer <span
								class="fa fa-long-arrow-right ml-3"></span></a>
						<a href="{{ route('register.type' , [ 'type' => 'housekeeper' ]) }}" class="btn btn-success">Signup as a Housekeeper <span
								class="fa fa-long-arrow-right ml-3"></span></a>
						<a href="{{ route('register.type' , [ 'type' => 'childminder' ]) }}" class="btn btn-success">Signup as a Childminder <span
								class="fa fa-long-arrow-right ml-3"></span></a>
						<a href="{{ route('register.type' , [ 'type' => 'client']) }}" class="btn btn-success">Signup as a Client <span
								class="fa fa-long-arrow-right ml-3"></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
