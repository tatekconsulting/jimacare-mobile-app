@extends('app.template.layout')

@section('content')
	<div class="full-width mt-5 pt-5">
		<div class="container">
			<div class="row">
				<div class="col-lg-7 col-md-12 col-sm-12 col-12">
					<div class="single-profile manage-profile mt-3 pb-5">
						<div class="d-flex pb-5">
							<div class="image-wrap">
								<img src="{{ asset('img/karim.png') }}" alt="">
							</div>
							<div class="profile-content">
								<div class="insured-pipe">
									<a href="#" class="primary-bgclr">Message</a>
									<p>Insured <span><img src="{{ asset('img/tick.svg') }}" alt=""></span></p>
								</div>
								<h3>Dana Underwood <span>/ Norwich</span></h3>
								<p>
									Leanguage<br>
									<span>English, Spanish, Tagalog</span>
								</p>

							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-5 col-md-12 col-sm-12 col-12">
					<div class="my-jobs">
						<h3>My Jobs</h3>
						<ul>
							<li>
								<div class="d-flex">
									<a href="#">Waking Night care required in Norwich NR15 11/09/2020</a>
									<span>/ Norwich City</span>
								</div>
								<a href="#">Edit</a>
							</li>
						</ul>

						<div class="new-job">
							<a href="#" class="primary-bgclr">New Job</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="profile-about my-5 pb-5">
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-12">
						<h3>About me</h3>
						<p>My interests are travelling, and have travelled well both UK and abroad. I love watching
							sports, such as football, tennis rugby and Formula1.<br>
							I like going to theatre, listening to music from pop to classical, going to concerts and
							museums. I am an animal lover most specially dogs</p>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
