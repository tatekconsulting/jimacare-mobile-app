<div class="footer">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12 col-12 mb-5">
				<div class="mb-3">
					<img src="{{ asset('img/logo-footer.png') }}" alt="" width="60">
				</div>
				<p>
					Email: support@jimacare.com<br/>
					Phone Number: 01182303044
				</p>
			</div>
			<div class="col-md-3 col-sm-4 col-12">
				<ul class="footer-nav">
					{{--<li>
						<a href="#">Service Provider Searching</a>
					</li>--}}
					<li>
						<a href="{{ route('termsCondition') }}">Terms & Conditions</a>
					</li>
					<li>
						<a href="{{ route('privacyPolicy') }}">Privacy Policy</a>
					</li>
					<li>
						<a href="{{ route('cookiePolicy') }}">Cookie Policy</a>
					</li>
					<li>
						<a href="{{ route('helpdesk') }}">Helpdesk</a>
					</li>
					<li>
						<a href="{{ route('about') }}">About</a>
					</li>
				</ul>
			</div>
			<div class="col-md-3 col-sm-4 col-12">
				<ul class="footer-nav">
					<li>
						<a href="#">Carers</a>
					</li>
					<li>
						<a href="#">Housekeeper</a>
					</li>
					<li>
						<a href="#">Childminder</a>
					</li>
				</ul>
				<div class="social-media">
					<ul>
						<li>
							<a href="https://www.facebook.com/jimacare">
								<i class="fa fa-facebook"></i>
							</a>
						</li>
						<li>
							<a href="https://twitter.com/jimacare1">
								<i class="fa fa-twitter"></i>
							</a>
						</li>
						<li>
							<a href="https://www.instagram.com/jima_care/">
								<i class="fa fa-instagram"></i>
							</a>
						</li>

						<li>
							<a href="https://www.youtube.com/channel/UCYe4V44qAyqHq8KEsP4mYiQ">
								<i class="fa fa-youtube"></i>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-3 col-sm-4 col-12">
				<h3>Subscribe to the newsletter</h3>
				<div class="newsletter">
					<div class="d-flex">
						<input type="text" placeholder="Your e-mail address">
						<button>
							<img src="{{ asset('img/arrow-footer.png') }}" alt="">
						</button>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-12 mt-4 text-center text-white">
				Lalyka Systems Limited. Company Number: 08341328 VAT Number: 332 5564 11<br/>
				JimaCare 2023 All rights reserved
			</div>
		</div>
	</div>
</div>
