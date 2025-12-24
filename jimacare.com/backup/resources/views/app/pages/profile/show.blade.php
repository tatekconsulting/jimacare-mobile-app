@extends('app.template.layout')

@section('content')
	<div class="pt-5">
		<div class="container">
			<div class="row">
				<div class="col-12 py-3 bg-white">
					<div class="row">
						<div class="seller-left col-12 col-md-3">
							<div class="seller-img">
								<img class="img img-fluid seller-avatar" src="{{ asset($user->profile ?? 'img/undraw_profile.svg') }}"
									 alt="{{ $user->firstname ?? '' }} {{ $user->lastname[0] ?? '' }}">
							</div>
						</div>
						<div class="seller-center col-12 col-md-6 text-center text-md-left">
							<h1>{{ $user->firstname ?? '' }} {{ $user->lastname[0] ?? '' }}</h1>
							<p>
								<span class="fa fa-map-marker mr-2"></span>
								{{ $user->city ?? '' }}, {{ $user->country ?? '' }}, {{ $user->postal ?? '' }}

								{{--@if($user->dob ?? false)
									<span class="profile-seperator">|</span>
									Age {{ \Carbon\Carbon::parse($user->dob)->age }} Years
								@endif--}}
							</p>
							<div class="profile-badge-info">
								@if($user->approved ?? false)
									<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Verified & Approved</span>
								@endif

								@if($user->insured ?? false)
									<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Insured</span>
								@endif

								@if($user->vaccinated ?? false)
									<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Vaccinated</span>
								@endif
							</div>
							<p>
								{{ $user->info ?? '' }}
							</p>
						</div>
						<div class="seller-right col-12 col-md-3 text-center text-md-right">
							@if(auth()->check())
								<button class="btn btn-interested badge-pill btn-block btn-primary float-md-right mb-1">Send Message</button>
								<form class="bg-light mini-msg-box d-none" method="POST" action="{{ route('message', [ 'user' => $user->id]) }}">
									@csrf
									<div class="input-group">
										<textarea name="message" class="form-control" id="message"
												  placeholder="What's on your mind..."
										></textarea>
									</div>
									<div class="input-group">
										<button class="btn btn-block btn-primary" type="submit">Send</button>
									</div>
								</form>
								<div class="text-center d-none msg-sent-notice py-3">
									<span class="fa fa-check-circle fa-2x"></span><br/>
									Message Sent
								</div>
							@else
								<a href="{{ route('login') }}" class="btn badge-pill btn-block btn-primary float-md-right mb-1">Send Message</a>
							@endif
						</div>

						{{--<div class="seller-left text-center col-12 col-md-auto">

						</div>

						<div class="col-12 col-md seller-right pt-3 pt-md-0">
							<div class="row no-gutters">

								<div class="col-6 text-right">

								</div>


							</div>
							--}}{{--<div class="row py-1">
								<h3 class="col-8">
									{{ $user->firstname }} {{ $user->lastname }}
								</h3>
								<div class="col-4 text-right">
									Logged in
									@if($user->last_login )
										{{ $user->last_login->diffForHumans() }}
									@else
										{{ today()->diffForHumans() }}
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									@if($user->gender ?? false)
										<span class="btn btn-sm btn-primary disabled">{{ ucfirst($user->gender) }}</span>
									@endif
									<span class="btn btn-sm btn-primary disabled">Member Since {{ $user->created_at->format('M Y') ?? '' }}</span>
									<span class="btn btn-sm btn-primary disabled">Last Updated {{ $user->updated_at->format('M Y') ?? '' }}</span>
								</div>
								<div class="col-12 mt-2">
									<a href="#" class="btn btn-primary px-4"><span class="fa fa-envelope mr-2"></span>Message</a>
								</div>
							</div>--}}{{--
						</div>--}}
					</div>
				</div>

				@if($user->video ?? false)
					<div class="col-12 my-2">
						<video class="w-100 d-block" style="max-width: 600px;" src="{{ asset($user->video ?? '')  }}" controls></video>
					</div>
				@endif

				<div class="col-12 pb-4">
					<div class="row">
						@if($user->experiences->count() > 0)
							<div class="col-12 pt-3">
								<h3>My Experience</h3>
								<div class="mb-1">
									@foreach($user->experiences as $exp)<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $exp->title }}</span>@endforeach
								</div>
							</div>
						@endif

						@if($user->skills->count() > 0)
							<div class="col-12 pt-3">
								<h3>My Skills</h3>
								<div class="mb-1">
									@foreach($user->skills as $skill)<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $skill->title }}</span>@endforeach
								</div>
							</div>
						@endif

						@if($user->educations->count() > 0)
							<div class="col-12 pt-3">
								<h3>My Qualifications</h3>
								<div class="mb-1">
									@foreach($user->educations as $edu)<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $edu->title }}</span>@endforeach
								</div>
							</div>
						@endif

						@if($user->interests->count() > 0)
							<div class="col-12 pt-3">
								<h3>My Interests</h3>
								<div class="mb-1">
									@foreach($user->interests as $interest)<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $interest->title }}</span>@endforeach
								</div>
							</div>
						@endif

						@if($user->availabilities->count() > 0)
							<div class="col-12 pt-3">
								<h3>Available For</h3>
								<div class="mb-1">
									@foreach($user->availabilities as $avail)<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $avail->type->title ?? '' }}</span>@endforeach
								</div>
							</div>
						@endif

						@if($user->days->count() > 0)
							<div class="col-12 pt-3">
								<h3>Working Days</h3>
								<div class="mb-3">
									@foreach($user->days as $day)<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $day->title }}</span>@endforeach
								</div>
							</div>
						@endif
						@if($user->time_availables->count() > 0)
							<div class="col-12 pt-3">
								<h3>Working Time</h3>
								<div class="table-responsive mb-3">
									<table class="table table-bordered table-striped  mb-0">
										<thead>
											<tr>
												<td></td>
												@foreach($days as $day)
													<td class="text-center">{{ $day->title }}</td>
												@endforeach
											</tr>
										</thead>
										<tbody>
											@foreach($time_types ?? [] as $time)
												<tr>
													<td>{{ $time->title }}</td>
													@foreach($days as $day)
														@php $avail = $user->time_availables->where('type_id', $time->id)->where('day_id', $day->id)->count(); @endphp
														<td class="text-center">
															<span class="fa fa-lg fa-{{ ($avail > 0) ? 'check-square-o text-success' : 'times-circle text-danger' }}"></span>
														</td>
													@endforeach
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						@endif
					</div>
				</div>
				<div class="col-12 pb-4">
					<h3>Reviews</h3>
					<p>
						Below are the latest reviews for {{ $user->name ?? '' }}. Please note that reviews represent the opinions of jimacare.com platform users and not of jimacare.com. Client must carry out their own checks on providers to ensure that they are completely happy before engaging in the use of their services.
					</p>

					@foreach($user->reviews ?? [] as $review)
						<div class="row pb-5">
							<div class="col-12">
								<div class="row no-gutters border-bottom">
									{{--<div class="col-9">
										<h5 class="text-primary font-weight-bold">{{ $review->title ?? '' }}</h5>
									</div>--}}
									<div class="col-12">
										<span class="rating raty readable" data-score="{{ $review->stars ?? 0}}"></span>
									</div>
								</div>
							</div>
							<div class="col-12 py-2">
								{{ $review->desc ?? '' }}
							</div>
							<div class="col-12">
								By {{ $review->client->firstname }} on {{ $review->created_at->format('d/m/Y') }}
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
@endsection
