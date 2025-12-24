@extends('app.template.layout-profile')

@section('content')
	<div class="pt-5">
		<div class="banner">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-6">
								<h2 class="mt-5 pb-4">Order #{{ $order->invoice_id  }}</h2>
							</div>
							<div class="col-md-6">
								<h3 class="mt-5 pb-4" style="padding-left: 55px; color:#2A9D8F">£{{ $order->payment->price ?? '' }}</h3>
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
						<h1>{{ $order->client->address }}</h1>
						{{--<div class="job-location">
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
									<p class="mt-4 d-block text-right">Overnight</p>
								</div>
							</div>
							<ul class="d-flex">
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
						</div>--}}
						<div class="care-likes">
							<h2>Job Description or Instructions</h2>
							<p>{{ $order->invoice->message->message ?? '' }}</p>
						</div>
						@if( (auth()->id() == $order->client_id) && ($order->status == 'completed') && (!($order->review->id ?? false)) )
							<div class="pb-5">
								<h3>Submit Review </h3>
								<!-- Rating Stars Box -->
								<div class="comment-content">
									<form action="{{ route('order.review', [ 'order' => $order->id ]) }}" method="post" class="uk-grid-small" uk-grid>
										@csrf
										<div class="uk-width-1-2@s">
											<label class="uk-form-label">Rate</label>
											<input type="hidden" name="stars" required>
											<div class='rating-stars text-center'>
												<ul id='stars'>
													<li class='star' title='Poor' data-value='1'>
														<i class='fa fa-star fa-2x'></i>
													</li>
													<li class='star' title='Fair' data-value='2'>
														<i class='fa fa-star fa-2x'></i>
													</li>
													<li class='star' title='Good' data-value='3'>
														<i class='fa fa-star fa-2x'></i>
													</li>
													<li class='star' title='Excellent' data-value='4'>
														<i class='fa fa-star fa-2x'></i>
													</li>
													<li class='star' title='WOW!!!' data-value='5'>
														<i class='fa fa-star fa-2x'></i>
													</li>
												</ul>
											</div>
										</div>
										<div class="uk-width-1-1@s">
											<label class="uk-form-label">Feedback</label>
											<textarea name="desc" class="uk-textarea" placeholder="Enter Your Comments here..." style=" height:160px" required></textarea>
										</div>
										<div class="uk-grid-margin">
											<button type="submit" value="submit">Submit</button>
										</div>
									</form>
								</div>
							</div>
						@elseif( $order->review->id ?? false )
							<div class="pb-4">
								<h2>Client Review</h2>

								@if($order->review ?? false)
									<div class="row pb-5">
										<div class="col-12">
											<div class="row no-gutters">
												<div class="col-12">
													<span class="rating raty readable" data-score="{{ $order->review->stars ?? 0}}"></span>
												</div>
											</div>
										</div>
										<div class="col-12 py-2">
											{{ $order->review->desc ?? '' }}
										</div>
										<div class="col-12">
											By {{ $order->review->client->name ?? '' }} on {{ $order->review->created_at->format('d/m/Y') }}
										</div>
									</div>
								@endif
							</div>
						@endif
					</div>

					<div class="col-md-6 col-sm-6 col-12">
						<div class="personal-detail">
							<h3>Details of Carer</h3>
							<ul>
								<li>
									<p>Full name</p>
									<span>{{ $order->seller->name ?? '' }}</span>
								</li>
								<li>
									<p>Age</p>
									<span>{{ $order->seller->age ?? '' }}</span>
								</li>
								<li>
									<p>Postcode</p>
									<span>{{ $order->seller->postal ?? '' }}</span>
								</li>
							</ul>

							<h3>Details of person requiring care</h3>
							<ul>
								<li>
									<p>Full name</p>
									<span>{{ $order->client->name ?? '' }}</span>
								</li>
								<li>
									<p>Age</p>
									<span>{{ $order->client->age ?? '' }}</span>
								</li>
								<li>
									<p>Postcode</p>
									<span>{{ $order->client->postcode ?? '' }}</span>
								</li>
								{{--<li>
									<p>Care Type</p>
									<span>-</span>
								</li>
								<li>
									<p>Starts When</p>
									<span>Not Sure</span>
								</li>--}}
							</ul>

							<h3>Order Status</h3>
							<ul>
								<li>
									<h5 class="w-100 text-info">{{ ucfirst($order->status) }}</h5>
								</li>
							</ul>

							@if(auth()->id() == $order->client_id)

								@if( $order->status == 'submitted' )
									<form method="post" action="{{ route('order.complete', [ 'order' => $order->id ]) }}">
										@csrf
										<button type="submit">Mark as Complete</button>
									</form>

									<form method="post" action="{{ route('order.revision', [ 'order' => $order->id ]) }}">
										@csrf
										<button type="submit">Request Revision</button>
									</form>
								@else

								@endif
								{{-- started, submitted, revision, completed, cancelled --}}
							@else
								@if( in_array($order->status, ['started', 'revision'] ) )
									<form method="post" action="{{ route('order.submit', [ 'order' => $order->id ])  }}">
										@csrf
										<button type="submit">Submit Order</button>
									</form>
								@endif
							@endif

							@if( $order->status == 'started' )

							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<style>
		/* Rating Star Widgets Style */
		.rating-stars ul {
			list-style-type:none;
			padding:0;
			-moz-user-select:none;
			-webkit-user-select:none;
		}
		.rating-stars ul > li.star {
			display:inline-block;
		}
		/* Idle State of the stars */
		.rating-stars ul > li.star > i {
			font-size:2.5em; /* Change the size of the stars */
			color:#ccc; /* Color on idle state */
		}
		/* Hover state of the stars */
		.rating-stars ul > li.star.hover > i {
			color:#FFCC36;
		}
		/* Selected state of the stars */
		.rating-stars ul > li.star.selected > i {
			color:#fdbe42;
		}
	</style>
@endsection
