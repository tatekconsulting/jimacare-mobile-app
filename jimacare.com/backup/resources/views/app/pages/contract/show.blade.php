@extends('app.template.layout')

@section('content')
	<div class="pt-5">
		<div class="container">
			<div class="row">
				<div class="col-12 py-3 bg-white">
					<div class="row">
						@if( (auth()->user()->role->slug ?? '') == 'admin' && $contract->user->profile)
							<div class="seller-left col-12 col-md-3">
								<div class="seller-img">
									<img class="img img-fluid seller-avatar" src="{{ asset($contract->user->profile) }}"
										 alt="{{ $contract->user->name ?? '' }}">
								</div>
							</div>
						@endif
						<div class="seller-center col-12 col-md-6 text-center text-md-left">
							<h1>{{ $contract->user->name ?explode(' ',trim($contract->user->name))[0]: '' }}</h1>
							<p>
								<span class="fa fa-map-marker mr-2"></span>{{ $contract->user->city ?? '' }}, {{ $contract->user->country ?? '' }}
								, {{ $contract->user->posttal ?? '' }}
								@if(auth()->user() && auth()->user()->role_id==1 && strlen($contract->company) > 0)
									<span class="profile-seperator">|</span>
									<span class="fa fa-building mr-2"></span> {{ $contract->company }}
								@endif
							</p>
							<div class="profile-badge-info">
								@if($contract->user->approved ?? false)
									<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Verified & Approved</span>
								@endif

								@if($contract->user->insured ?? false)
									<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Insured</span>
								@endif

								@if($contract->user->vaccinated ?? false)
									<span class="profile-badge bg-primary text-white"><span class="fa fa-check-circle"></span>Vaccinated</span>
								@endif
							</div>
							<p>
								{{ $contract->desc ?? '' }}
							</p>
						</div>
						<div class="seller-right col-12 col-md-3 text-center text-md-right">
							@if(auth()->check())
								<button class="btn btn-interested badge-pill btn-block btn-primary float-md-right mb-1">Interested</button>
								<form class="bg-light mini-msg-box d-none" method="POST" action="{{ route('message', [ 'user' => $contract->user_id]) }}">
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
							@else
								<a href="{{ route('login') }}" class="btn badge-pill btn-block btn-primary float-md-right mb-1">Interested</a>
							@endif
							<button class="btn btn-not-interested badge-pill btn-block btn-danger float-md-right">Not Interested</button>
							@if(auth()->check())
								<div class="text-center d-none msg-sent-notice py-3">
									<span class="fa fa-check-circle fa-2x"></span><br/>
									Message Sent
								</div>
							@endif
						</div>
					</div>
				</div>

				<div class="col-12 pt-3 pb-5">
					<div class="row">
						<div class="col-12 col-md-6">
							<h4>When to join</h4>
							<p>
								@if($contract->start_type == 'immediately')
									Immediately
								@elseif($contract->start_type == 'not-sure')
									Not Sure
								@else
									{{ $contract->start_date->format('d M Y') }}
								@endif
							</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>When to end</h4>
							<p>
								@if($contract->end_type == 'on-going')
									On Going
								@else
									{{ $contract->end_date->format('d M Y') }}
								@endif
							</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>Time of Arrival</h4>
							<p>{{ $contract->start_time->format('H:i') }}</p>
						</div>

						<div class="col-12 col-md-6">
							<h4>Time of Leave</h4>
							<p>{{ $contract->end_time->format('H:i') }}</p>
						</div>

						@if($contract->role->id == 4)
							<div class="col-12 col-md-12">
								<h4>Required to Drive?</h4>
								<p>{{ $contract->drive ? 'Yes' : 'No' }}</p>
							</div>
						@endif

						@if($contract->role->id == 3)
							<div class="col-12 pt-3">
								<h3>Services Type</h3>
								<div class="mb-1">
									@foreach($contract->types as $type)
										<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $type->title }}</span>
									@endforeach
								</div>
							</div>
						@endif

						@if($contract->role->id == 5)
							<div class="col-12 col-md-6">
								<h4>How often do you need cleaning?</h4>
								<p>{{ $contract->how_often ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many bedroom(s) need cleaning?</h4>
								<p>{{ $contract->beds ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many bathroom(s) need cleaning?</h4>
								<p>{{ $contract->baths ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>How many reception room(s) need cleaning?</h4>
								<p>{{ $contract->rooms ?? '' }}</p>
							</div>
							<div class="col-12 col-md-6">
								<h4>What type of cleaning would you like?</h4>
								<p>{{ $contract->cleaning_type ?? '' }}</p>
							</div>
						@endif

						@if(in_array($contract->role_id, [3,5]))
							<div class="col-12">
								<h3>Working Days</h3>
								<div class="mb-1">
									@foreach($contract->days as $day)
										<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $day->title }}</span>
									@endforeach
								</div>
							</div>
						@endif

						@if($contract->role_id == 4 )
							<div class="col-12">
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
													@php $avail = $contract->time_availables->where('type_id', $time->id)->where('day_id', $day->id)->count(); @endphp
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

						<div class="col-12">
							<h3>Languages</h3>
							<div class="mb-1">
								@foreach($contract->languages as $language)
									<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $language->title }}</span>
								@endforeach
							</div>
						</div>

						<div class="col-12">
							<h3>Experiences Required</h3>
							<div class="mb-1">
								@foreach($contract->experiences as $exp)
									<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $exp->title }}</span>
								@endforeach
							</div>
						</div>
						@if($contract->role_id == 4)
							<div class="col-12">
								<h3>Interests</h3>
								<div class="mb-1">
									@foreach($contract->interests as $interest)
										<span class="px-3 py-2 bg-primary text-white rounded-pill d-inline-block mr-2 mb-2">{{ $interest->title }}</span>
									@endforeach
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
